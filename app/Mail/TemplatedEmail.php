<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class TemplatedEmail extends Mailable
{
  use Queueable, SerializesModels;

  public function __construct(
    public string $template,
    public string $subjectLine,
    public array  $data = [],
    public ?array $inlineImages = []
  ) {}

  public function build()
  {
    $mail = $this->subject($this->subjectLine)
      ->view("emails.{$this->template}", $this->data);
    if (!empty($this->inlineImages)) {
      $mail->withSymfonyMessage(function (Email $message) {
        foreach ($this->inlineImages as $cid => $path) {
          $message->embedFromPath($path, $cid);
        }
      });
    }
    return $mail;
  }
}
