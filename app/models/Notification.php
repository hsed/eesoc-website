<?php

class Notification {

  /**
     * Locker sale received email.
     * @param  User   $user
     * @return integer
     */
    public static function sendLockerInformation(User $user)
    {
        extract(static::mailInstances());

        $message->setFrom(array('locker@eesoc.com' => 'EESoc Locker Team'));
        $message->setReplyTo('please-reply@eesoc.com');
        $message->setTo($user->email);
        $message->setSubject('Locker Ready To Be Claimed');

        $html = View::make('emails.locker_notification')
            ->with('user', $user)
            ->render();
        $message->setBody($html, 'text/html');
        $message->addPart('We have received your locker order. Your locker is now ready to be claimed.', 'text/plain');

        return $mailer->send($message);
    }

    /**
     * Remind users with unclaimed lockers
     * @param  User   $user
     * @return integer
     */
    public static function sendLockerClaimReminder(User $user)
    {
        extract(static::mailInstances());

        $subject = sprintf(
            'You have unclaimed %d %s',
            $user->unclaimed_lockers_count,
            Str::plural('unclaimed locker', $user->unclaimed_lockers_count)
        );

        $message->setFrom(array('locker@eesoc.com' => 'EESoc Locker Team'));
        $message->setReplyTo('please-reply@eesoc.com');
        $message->setTo($user->email);
        $message->setSubject($subject);

        $html = View::make('emails.locker_claim_reminder')
            ->with('subject', $subject)
            ->with('user', $user)
            ->render();
        $message->setBody($html, 'text/html');
        $message->addPart('Your locker is now ready to be claimed.', 'text/plain');

        return $mailer->send($message);
    }

    /**
     * Send locker terms and conditions
     * @param  User   $user
     * @return integer
     */
    public static function sendLockerTermsAndConditions(User $user)
    {
        extract(static::mailInstances());

        $subject = 'Locker Terms and Conditions';

        $message->setFrom(array('locker@eesoc.com' => 'EESoc Locker Team'));
        $message->setReplyTo('please-reply@eesoc.com');
        $message->setTo($user->email);
        $message->setSubject($subject);

        $html = View::make('emails.locker_terms_and_conditions')
            ->with('subject', $subject)
            ->with('user', $user)
            ->render();
        $message->setBody($html, 'text/html');
        $message->addPart('Locker terms and conditions. Please read!', 'text/plain');

        return $mailer->send($message);
    }

    /**
     * Send locker terms and conditions
     * @param  User   $user
     * @return integer
     */
    public static function sendLockerIssues(User $user)
    {
        extract(static::mailInstances());

        $subject = 'Locker Issues Reporting';

        $message->setFrom(array('locker@eesoc.com' => 'EESoc Locker Team'));
        $message->setReplyTo('please-reply@eesoc.com');
        $message->setTo($user->email);
        $message->setSubject($subject);

        $html = View::make('emails.locker_issues_report')
            ->with('subject', $subject)
            ->with('user', $user)
            ->render();
        $message->setBody($html, 'text/html');
        $message->addPart('Locker Issues Reporting. Please read!', 'text/plain');

        return $mailer->send($message);
    }

	 public static function sendLockerClear(User $user, String $locker_name)
    {
        extract(static::mailInstances());
			

        $subject = 'Clearing Your Locker ('.$locker_name.')';

        $message->setFrom(array('lockers@eesoc.com' => 'EESoc Locker Team'));
        $message->setReplyTo('lockers@eesoc.com');
        $message->setTo($user->email);
        $message->setSubject($subject);

        $html = View::make('emails.locker_clear')
            ->with('subject', $subject)
            ->with('user', $user)
			->with('locker_name', $locker_name)
            ->render();
        $message->setBody($html, 'text/html');
        $message->addPart('End of year locker procedures. Please read!', 'text/plain');

        return $mailer->send($message);
    }


    /**
     * Returns mail transport, mailer and message instance.
     * @return array
     */
    private static function mailInstances()
    {
        $transport = Swift_MailTransport::newInstance();
        $mailer = Swift_Mailer::newInstance($transport);
        $message = Swift_Message::newInstance();

        return compact('transport', 'mailer', 'message');
    }

}
