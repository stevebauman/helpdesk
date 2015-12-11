<?php

namespace App\Processors;

use App\Http\Requests\IssueLabelRequest;
use App\Models\Issue;
use App\Models\Label;

class IssueLabelProcessor extends Processor
{
    /**
     * Constructor.
     *
     * @param Issue $issue
     * @param Label $label
     */
    public function __construct(Issue $issue, Label $label)
    {
        $this->issue = $issue;
        $this->label = $label;
    }

    /**
     * Updates the specified issue labels.
     *
     * @param IssueLabelRequest $request
     * @param int|string        $id
     *
     * @return bool
     */
    public function store(IssueLabelRequest $request, $id)
    {
        $issue = $this->issue->findOrFail($id);

        $this->authorize('addLabels', $issue);

        if ($request->has('labels')) {
            $labels = $this->label->find($request->input('labels'));

            $issue->labels()->sync($labels);

            return true;
        }

        $issue->labels()->detach();

        return true;
    }
}
