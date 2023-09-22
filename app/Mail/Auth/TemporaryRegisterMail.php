<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemporaryRegisterMail extends Mailable
{
  use Queueable, SerializesModels;

  public $user;
  public $frontend_url;
  // public $reserve_detail;
  // public $dental_data;
  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($user)
  {
    $this->user = $user;
    $this->frontend_url = config('app.frontend_url');
  }

  /**
   * Get the message envelope.
   *
   * @return \Illuminate\Mail\Mailables\Envelope
   */
  public function envelope()
  {
    return new Envelope(
      subject: '仮登録が完了しました。本登録を済ませましょう。',
      from: 'wolf@example.net',
    );
  }

  /**
   * Get the message content definition.
   *
   * @return \Illuminate\Mail\Mailables\Content
   */
  public function content()
  {
    return new Content(
      view: 'emails.auth.temporary_register',
    );
  }

  /**
   * Get the attachments for the message.
   *
   * @return array
   */
  public function attachments()
  {
    return [];
  }
}
