<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\GuideRequest;
use App\Models\Guide;
use App\Processors\Resource\GuideProcessor;

class GuideController extends Controller
{
    /**
     * @var GuideProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param GuideProcessor $processor
     */
    public function __construct(GuideProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Displays all guides.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->processor->index();
    }

    /**
     * Displays the form for creating a new guide.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->processor->create();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param GuideRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GuideRequest $request)
    {
        $guide = $this->processor->store($request);

        if ($guide instanceof Guide) {
            flash()->success('Success!', 'Successfully created guide!');

            return redirect()->route('resources.guides.index');
        } else {
            flash()->error('Error!', 'There was an issue creating a guide. Please try again.');

            return redirect()->route('resources.guides.create');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int|string $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return $this->processor->show($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int|string $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return $this->processor->edit($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GuideRequest $request
     * @param int|string   $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(GuideRequest $request, $id)
    {
        $guide = $this->processor->update($request, $id);

        if ($guide instanceof Guide) {
            flash()->success('Success!', 'Successfully updated guide!');

            return redirect()->route('resources.guides.show', [$guide->getSlug()]);
        } else {
            flash()->error('Error!', 'There was an issue updating this guide. Please try again.');

            return redirect()->route('resources.guides.edit', [$id]);
        }
    }

    /**
     * Deletes the specified guide.
     *
     * @param int|string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($this->processor->destroy($id)) {
            flash()->success('Success!', 'Successfully deleted guide!');

            return redirect()->route('resources.guides.index');
        } else {
            flash()->error('Error!', 'There was an issue deleting this guide. Please try again.');

            return redirect()->route('resources.guides.show', [$id]);
        }
    }
}
