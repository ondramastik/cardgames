<?php

namespace App\Models\Chat;


class Message implements \IPub\WebSockets\Application\Responses\IResponse {

    /** @var string */
    private $sender;

    /** @var \DateTime */
    private $time;

    /** @var string */
    private $text;

    /** @var boolean */
    private $otherSender;

    /**
     * Message constructor.
     * @param string $sender
     * @param \DateTime $time
     * @param string $text
     */
    public function __construct(string $sender, \DateTime $time, string $text) {
        $this->sender = $sender;
        $this->time = $time;
        $this->text = $text;
        $this->otherSender = true;
    }

    function create(): ?array {
        return [
            'sender' => $this->sender,
            'time' => $this->time->format('H:i'),
            'text' => $this->text,
            'isOtherSender' => $this->otherSender,
        ];
    }

    /**
     * @param bool $otherSender
     */
    public function setOtherSender(bool $otherSender): void {
        $this->otherSender = $otherSender;
    }

    public function __toString() {
        return "[{$this->time->format('H:i')}] $this->sender: $this->text";
    }

}