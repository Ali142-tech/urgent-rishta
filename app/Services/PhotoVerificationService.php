<?php

namespace App\Services;

use Aws\Rekognition\RekognitionClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

/**
 * Automated (AI) photo verification — replaces manual admin review per
 * client request. Compares a member's live-captured selfie against their
 * uploaded profile photo using AWS Rekognition's face comparison, so no
 * human ever has to eyeball the pair.
 */
class PhotoVerificationService
{
    protected RekognitionClient $client;
    protected float $threshold;

    public function __construct()
    {
        $this->client = new RekognitionClient([
            'version' => 'latest',
            'region' => config('services.rekognition.region'),
            'credentials' => [
                'key' => config('services.rekognition.key'),
                'secret' => config('services.rekognition.secret'),
            ],
        ]);
        $this->threshold = (float) config('services.rekognition.similarity_threshold', 80);
    }

    /**
     * Compare a selfie against a reference profile photo.
     *
     * Returns ['matched' => bool, 'similarity' => float|null, 'error' => string|null].
     * 'error' is set (and 'matched' false) when Rekognition itself couldn't
     * process the images at all (e.g. no face detected in one of them, or
     * the API call failed) — distinct from a clean comparison that simply
     * scored below the threshold — so callers can phrase the rejection
     * reason appropriately either way.
     */
    public function compareFaces(string $selfiePath, string $referencePhotoPath): array
    {
        if (!file_exists($selfiePath) || !file_exists($referencePhotoPath)) {
            return ['matched' => false, 'similarity' => null, 'error' => 'One or both photos could not be found.'];
        }

        try {
            $result = $this->client->compareFaces([
                'SourceImage' => ['Bytes' => file_get_contents($selfiePath)],
                'TargetImage' => ['Bytes' => file_get_contents($referencePhotoPath)],
                'SimilarityThreshold' => $this->threshold,
            ]);

            $matches = $result['FaceMatches'] ?? [];
            if (empty($matches)) {
                // Rekognition processed both images fine but found no match
                // at/above the threshold — a clean "doesn't match" result,
                // not an error.
                return ['matched' => false, 'similarity' => 0.0, 'error' => null];
            }

            $best = collect($matches)->sortByDesc('Similarity')->first();
            $similarity = (float) ($best['Similarity'] ?? 0);

            return [
                'matched' => $similarity >= $this->threshold,
                'similarity' => $similarity,
                'error' => null,
            ];
        } catch (AwsException $e) {
            // Most commonly InvalidParameterException — no face detected in
            // one of the two images (too dark, side angle, etc.).
            Log::error('PhotoVerificationService: Rekognition compareFaces failed: ' . $e->getAwsErrorMessage());
            return ['matched' => false, 'similarity' => null, 'error' => $e->getAwsErrorMessage() ?: 'Face comparison service error.'];
        } catch (\Throwable $e) {
            Log::error('PhotoVerificationService: unexpected error: ' . $e->getMessage());
            return ['matched' => false, 'similarity' => null, 'error' => 'Unexpected error during face comparison.'];
        }
    }
}
