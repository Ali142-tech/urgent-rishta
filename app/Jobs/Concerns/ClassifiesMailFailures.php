<?php

namespace App\Jobs\Concerns;

use Throwable;

/**
 * Shared by every per-recipient campaign-mail job. Used to only check
 * Symfony/Swift SMTP-style "got code \"5xx\"" text, which never matches
 * once a mailer switches to an API-based transport like SES — every SES
 * failure was silently misclassified as temporary and burned through the
 * job's full retry/backoff schedule instead of failing fast. Aws\Exception\
 * AwsException (what the SES API transport actually throws, usually wrapped
 * in a Symfony TransportException) is checked first; the SMTP pattern stays
 * as a fallback so nothing breaks if a mailer ever reverts to SMTP.
 */
trait ClassifiesMailFailures
{
    /**
     * SES error codes that mean "this will never succeed" (bad/rejected
     * address, unverified sending domain, suspended account) vs. ones worth
     * retrying (throttling, transient AWS-side issues).
     */
    private function isPermanentMailFailure(Throwable $e): bool
    {
        for ($cause = $e; $cause !== null; $cause = $cause->getPrevious()) {
            if ($cause instanceof \Aws\Exception\AwsException) {
                $permanentSesCodes = [
                    'MessageRejected',
                    'MailFromDomainNotVerifiedException',
                    'ConfigurationSetDoesNotExistException',
                    'InvalidParameterValue',
                    'AccountSuspendedException',
                ];
                return in_array($cause->getAwsErrorCode(), $permanentSesCodes, true);
            }
        }

        $message = $e->getMessage();

        if (preg_match('/got code "(\d{3})"/', $message, $m)) {
            return $m[1][0] === '5';
        }

        return (bool) preg_match('/\b5\d{2}\b/', $message) && !preg_match('/\b4\d{2}\b/', $message);
    }
}
