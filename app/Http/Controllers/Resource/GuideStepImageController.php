<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use App\Processors\Resource\GuideStepImageProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GuideStepImageController extends Controller
{
    /**
     * @var GuideStepImageProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param GuideStepImageProcessor $processor
     */
    public function __construct(GuideStepImageProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Returns a download response for the specified guide step image.
     *
     * @param int|string $id
     * @param int|string $stepId
     * @param string     $fileUuid
     *
     * @return BinaryFileResponse
     */
    public function download($id, $stepId, $fileUuid)
    {
        $response = $this->processor->download($id, $stepId, $fileUuid);

        if ($response instanceof BinaryFileResponse) {
            return $response;
        }

        abort(404);
    }

    /**
     * Deletes the specified guide step image.
     *
     * @param int|string $id
     * @param int|string $stepId
     * @param int|string $fileUuid
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id, $stepId, $fileUuid)
    {
        if ($this->processor->destroy($id, $stepId, $fileUuid)) {
            flash()->success('Success!', 'Successfully deleted image.');

            return redirect()->back();
        } else {
            flash()->error('Error!', 'There was an issue deleting this image. Please try again.');

            return redirect()->back();
        }
    }
}
