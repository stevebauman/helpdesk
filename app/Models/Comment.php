<?php

namespace App\Models;

use App\Models\Traits\HasMarkdownTrait;
use App\Models\Traits\HasUserTrait;
use App\Traits\CanPurifyTrait;
use Orchestra\Support\Facades\HTML;

class Comment extends Model
{
    use HasUserTrait, HasMarkdownTrait, CanPurifyTrait;

    /**
     * The comment table.
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * The fillable comment attributes.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'content'];

    /**
     * Returns true / false if the comment is a resolution.
     *
     * @return bool
     */
    public function isResolution()
    {
        if ($this->pivot) {
            return $this->pivot->resolution;
        }

        return false;
    }

    /**
     * Set the comments content.
     *
     * @param $content
     */
    public function setContentAttribute($content)
    {
        $this->attributes['content'] = $this->clean($content);
    }

    /**
     * Displays the commented 'daysAgo' tag line for comments.
     *
     * @return string
     */
    public function getTagLine()
    {
        $daysAgo = $this->createdAtHuman();

        if ($this->isResolution()) {
            $line = "created resolution $daysAgo";
        } else {
            $line = "commented $daysAgo";
        }

        return HTML::create('span', $line, ['class' => 'text-muted']);
    }

    /**
     * Returns content from markdown to HTML.
     *
     * @return string
     */
    public function getContentFromMarkdown()
    {
        return $this->fromMarkdown($this->content);
    }
}
