<?php

namespace App\Models\Bang\Handlers;


abstract class Handler {
	
    /** @var bool */
    protected $hasEventFinished = false;

    /**
     * @return bool
     */
    public function hasHandlerFinished(): bool {
        return $this->hasEventFinished;
    }

	/**
	 * @param bool $hasEventFinished
	 */
    public function setHasEventFinished(bool $hasEventFinished): void {
        $this->hasEventFinished = $hasEventFinished;
    }

}