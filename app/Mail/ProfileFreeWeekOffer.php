<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * "Complete your profile to 100% and get 1 week free access" email. Not ShouldQueue on purpose (sent from inside the
 * rate-limited SendFreeWeekOfferEmailJob). $missing is ProfileWizard::status()['missing'] (section title + field label).
 */
class ProfileFreeWeekOffer extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $firstName, public int $percent, public array $missing)
    {
    }

    public function build()
    {
        // group the missing fields by section, and cap the list so a very empty profile doesn't make a giant email
        $groups = [];
        foreach ($this->missing as $m) {
            $groups[$m['title']][] = $m['label'];
        }
        $total = count($this->missing);
        $shown = 0;
        $limited = [];
        foreach ($groups as $title => $labels) {
            if ($shown >= 14) {
                break;
            }
            $take = array_slice($labels, 0, 14 - $shown);
            $limited[$title] = $take;
            $shown += count($take);
        }

        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('Complete your profile and get 1 week FREE access')
            ->view('mail.profile-free-week-offer', [
                'groups' => $limited,
                'moreCount' => max(0, $total - $shown),
                'completeUrl' => url('member/profile/complete'),
            ]);
    }
}
