<?php

namespace App\Controllers;

use App\Services\DummyService;
use Illuminate\View\Factory as ViewFactory;

class DeveloperController extends Controller
{
    protected $dummyService;

    public function __construct(ViewFactory $viewFactory)
    {
        parent::__construct($viewFactory);
    }

    public function index()
    {
        $data = $this->dummyService->getData();

        return $this->view('home', ['name' => 'Brijesh']);
    }

    function clearViewsCache()
    {
        $cachePath = __DIR__ . '/../cache/views';

        // Get all files in the cache directory
        $files = glob("$cachePath/*");

        // Iterate over each file and delete it
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        // Optionally, you can delete the compiled views directory if it exists
        $compiledViewsPath = "$cachePath/compiled";
        if (is_dir($compiledViewsPath)) {
            $files = glob("$compiledViewsPath/*");
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($compiledViewsPath);
        }

        //dump('Views cache cleared successfully.');
        return 'Views cache cleared successfully.';
    }


}
