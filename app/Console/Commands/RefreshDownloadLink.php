<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Traits\FileFullPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RefreshDownloadLink extends Command
{

    use FileFullPath;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command Re-Generate Download Link of  File';

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
        $files = File::query()->get();

        foreach ($files as $file) {

            $fullPath = $this->ResolveFileFullPath($file);

            $url = Storage::temporaryUrl($fullPath, now()->addSeconds(604800));

            $file->update([
                'link' => 'test url'
            ]);

            return $this->info('Re-generate download link was complete .');

            // RegenerateDownloadLink::dispatch($file);
        }
    }
}
