<?php

namespace App\Models;

use App\Models\Traits\HasMarkdownTrait;
use App\Models\Traits\HasUserTrait;
use App\Traits\CanPurifyTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchestra\Support\Facades\HTML;

class Issue extends Model
{
    use HasUserTrait, CanPurifyTrait, HasMarkdownTrait, SoftDeletes;

    /**
     * The issues table.
     *
     * @var string
     */
    protected $table = 'issues';

    /**
     * The issue labels pivot table.
     *
     * @var string
     */
    protected $tablePivotLabels = 'issue_labels';

    /**
     * The issue comments pivot table.
     *
     * @var string
     */
    protected $tablePivotComments = 'issue_comments';

    /**
     * The issue users pivot table.
     *
     * @var string
     */
    protected $tablePivotUsers = 'issue_users';

    /**
     * The fillable issue attributes.
     *
     * @var array
     */
    protected $fillable = ['title', 'description'];

    /**
     * The fields that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'closed_at',
        'occurred_at',
    ];

    /**
     * Sets the issue's description attribute.
     *
     * @param string $description
     */
    public function setDescriptionAttribute($description)
    {
        $this->attributes['description'] = $this->clean($description);
    }

    /**
     * Sets the issue's occurred at date.
     *
     * @param string $occurredAt
     */
    public function setOccurredAtAttribute($occurredAt)
    {
        if (!empty($occurredAt)) {
            $date = $this->freshTimestamp();
            $date->modify($occurredAt);

            $this->attributes['occurred_at'] = $date;
        }
    }

    /**
     * The belongsToMany labels relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function labels()
    {
        return $this->belongsToMany(Label::class, $this->tablePivotLabels);
    }

    /**
     * The belongsToMany users relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, $this->tablePivotUsers);
    }

    /**
     * The belongsToMany comments relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function comments()
    {
        return $this->belongsToMany(Comment::class, $this->tablePivotComments)->withPivot(['resolution']);
    }

    /**
     * Adds a comment to an issue.
     *
     * @param string $content
     * @param bool   $resolution
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createComment($content, $resolution = false)
    {
        $attributes = [
            'content' => $content,
            'user_id' => auth()->user()->getAuthIdentifier(),
        ];

        // Make sure we only allow one comment resolution
        if ($this->hasCommentResolution()) {
            $resolution = false;
        }

        return $this->comments()->create($attributes, compact('resolution'));
    }

    /**
     * Updates the specified comment.
     *
     * @param int|string $commentId
     * @param int|string $content
     * @param bool|false $resolution
     *
     * @return bool
     */
    public function updateComment($commentId, $content, $resolution = false)
    {
        $comment = $this->comments()->findOrFail($commentId);

        // // Make sure we only allow one comment resolution
        if (!$this->hasCommentResolution() || $comment->isResolution()) {
            $this->comments()->updateExistingPivot($comment->getKey(), compact('resolution'));
        }

        $comment->content = $content;

        return $comment->save();
    }

    /**
     * Returns the issues comment resolution.
     *
     * @return null|Comment
     */
    public function findCommentResolution()
    {
        return $this->comments()->wherePivot('resolution', true)->first();
    }

    /**
     * Returns true / false if the current issue has a resolution.
     *
     * @return bool
     */
    public function hasCommentResolution()
    {
        return $this->findCommentResolution() ? true : false;
    }

    /**
     * Adds a label to an issue.
     *
     * @param Label $label
     *
     * @return bool
     */
    public function addLabel(Label $label)
    {
        $this->labels()->attach($label);

        return true;
    }

    /**
     * Returns true or false if the issue is closed.
     *
     * @return bool
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * Returns true or false if the issue is open.
     *
     * @return bool
     */
    public function isOpen()
    {
        return !$this->closed;
    }

    /**
     * Returns the status label of the issue.
     *
     * @return string
     */
    public function getStatusLabel()
    {
        if ($this->isOpen()) {
            $status = 'Open';
            $class = 'btn btn-success disabled';
        } else {
            $status = 'Closed';
            $class = 'btn btn-danger disabled';
        }

        return HTML::create('span', $status, compact('class'));
    }

    /**
     * Returns the tag line of the issue.
     *
     * @return string
     */
    public function getTagLine()
    {
        $user = $this->user->fullname;

        $daysAgo = $this->createdAtHuman();

        $comments = count($this->comments);

        return "$user opened this issue $daysAgo - $comments Comment(s)";
    }

    /**
     * Returns the description from markdown to HTML.
     *
     * @return string
     */
    public function getDescriptionFromMarkdown()
    {
        return $this->fromMarkdown($this->description);
    }

    /**
     * Returns the issues ID with a proceeding hash.
     *
     * @return string
     */
    public function getHashId()
    {
        return '#'.$this->getKey();
    }

    /**
     * Returns the created at time in a human readable format.
     *
     * @return string|null
     */
    public function closedAtHuman()
    {
        if ($this->closed_at) {
            return $this->closed_at->diffForHumans();
        }

        return;
    }

    /**
     * Returns the occurred at time in a human readable format.
     *
     * @return string|null
     */
    public function occurredAtHuman()
    {
        if ($this->occurred_at) {
            return $this->occurred_at->diffForHumans();
        }

        return;
    }

    /**
     * Returns the occurred at time for the date selector input.
     *
     * @return string|null
     */
    public function occurredAtForInput()
    {
        if ($this->occurred_at) {
            return $this->occurred_at->format('m/d/Y g:i A');
        }

        return;
    }
}
