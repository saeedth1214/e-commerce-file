<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Intervention\Image\Facades\Image;
use phpDocumentor\Reflection\Types\This;

class ImageOptimizerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimizer:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'optimize images in storage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $public_folder = storage_path('app/public');
        $folders = scandir($public_folder);

        unset($folders[array_search('.', $folders, true)]);
        unset($folders[array_search('..', $folders, true)]);
        unset($folders[array_search('.gitignore', $folders, true)]);

        // prevent empty ordered elements
        if (count($folders) < 1)
            return;

        foreach ($folders as $folder) {
            $folder_path = $public_folder . '/' . $folder;

            $images = glob($folder_path . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

            foreach ($images as $image) {
                $img = Image::make($image)->resize(300, 200);
                // save file as jpg with medium quality
                $img->save($image, 60);
            }
        }

        return $this->info('Process successfully ended.');
    }
}
