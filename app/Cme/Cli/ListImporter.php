<?php
namespace App\Cme\Cli;

use App\Cme\Helpers\ApiImporter;
use App\Cme\Helpers\CsvImporter;
use App\Cme\Lib\Cli\LongRunningScript;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputArgument;

class ListImporter extends LongRunningScript
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'cme:list-import';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Consumes import queue to import a list of subscribers';

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
   * @return mixed
   */
  public function handle()
  {
    $this->_init();
    $this->_createPIDFile();
    $instanceName = $this->_getInstanceName();
    while(true)
    {
      $result = DB::select(
        sprintf(
          "SELECT * FROM import_queue WHERE locked_by='%s' ORDER BY id ASC LIMIT 1",
          $instanceName
        )
      );

      if($result)
      {
        $importRequest = $result[0];
        $importer      = null;
        switch($importRequest->type)
        {
          case 'api':
            $importer = new ApiImporter();
            break;
          case 'csv':
            $importer = new CsvImporter();
            break;
        }

        if($importer !== null)
        {
          echo "Attempting the import" . PHP_EOL;
          $importer->import(
            $importRequest->source,
            $importRequest->list_id
          );
        }
        else
        {
          Log::error("Import Failed. Invalid import type.");
        }

        DB::table('import_queue')
          ->where(['id' => $importRequest->id])
          ->delete();
      }
      else
      {
        //lock a row
        $lockedARow = DB::update(
          "UPDATE import_queue SET locked_by=?
          WHERE locked_by IS NULL ORDER BY id ASC LIMIT 1",
          [$instanceName]
        );
        if(!$lockedARow)
        {
          $this->info("sleeping for a bit");
          sleep(2);
        }
      }
      if(!$lockedARow)
      {
        $this->_cronBailOut();
      }
    }
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return array(
      array('inst', InputArgument::REQUIRED, 'Instance Name'),
    );
  }

  private function _getInstanceName()
  {
    $inst = $this->argument('inst');
    return gethostname() . '-' . $inst;
  }
}
