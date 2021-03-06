<?php

namespace App\Exports;

use App\Services\CommissionService;
use App\Services\DepartmentService;
use App\Services\RankService;
use App\Services\UserService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExampleExporter implements FromCollection, WithHeadings, WithEvents
{
    /**
     * @var CommissionService
     */
    private $commissionService;

    /**
     * @var DepartmentService
     */
    private $departmentService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var RankService
     */
    private $rankService;

    /**
     * UsersExampleExporter constructor.
     */
    public function __construct()
    {
        $this->commissionService = app(CommissionService::class);
        $this->departmentService = app(DepartmentService::class);
        $this->userService = app(UserService::class);
        $this->rankService = app(RankService::class);

        $this->countRows = 500;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return new Collection();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [__('messages.report.fullName'), 'Email', __('messages.report.commission'), __('messages.report.department'),
            __('messages.report.rank'), __('messages.report.pedagogical'), __('messages.report.hiringYear'), 
            __('messages.report.experience'), __('messages.report.scientificDegreeY'), __('messages.report.scientificDegreeYear'), 
            __('messages.report.academicStatusY'), __('messages.report.academicStatusYear')];
    }

    /**
     * @param $sheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function createRanges($sheet){
        //get data from repositories
        $commissions = $this->commissionService->getForExportList();
        $departments = $this->departmentService->getForExportList();
        $pedagogicals = to_export_list(\Constants::$pedagogicalTitles, true);
        $ranks = $this->rankService->getForExportList();
        $academics = to_export_list(\Constants::$academicStatusList, true);
        $scientifics = to_export_list(\Constants::$scientificDegreeList, true);

        //set data to cells
        for($i = 1; $i <= sizeof($academics); $i++)
            $sheet->getCell("U$i")->setValue($academics[$i - 1]);

        for($i = 1; $i <= sizeof($scientifics); $i++)
            $sheet->getCell("V$i")->setValue($scientifics[$i - 1]);

        for($i = 1; $i <= sizeof($commissions); $i++)
            $sheet->getCell("W$i")->setValue($commissions[$i - 1]);

        for($i = 1; $i <= sizeof($departments); $i++)
            $sheet->getCell("X$i")->setValue($departments[$i - 1]);

        for($i = 1; $i <= sizeof($pedagogicals); $i++)
            $sheet->getCell("Y$i")->setValue($pedagogicals[$i - 1]);

        for($i = 1; $i <= sizeof($ranks); $i++)
            $sheet->getCell("Z$i")->setValue($ranks[$i - 1]);

        //create ranges
        $sheet->getParent()->addNamedRange( new NamedRange('scientifics',
            $sheet->getDelegate(), '$V$1:$V$' . sizeof($scientifics)) );

        $sheet->getParent()->addNamedRange( new NamedRange('academics',
            $sheet->getDelegate(), '$U$1:$U$' . sizeof($academics)) );

        $sheet->getParent()->addNamedRange( new NamedRange('commissions',
            $sheet->getDelegate(), '$W$1:$W$' . sizeof($commissions)) );

        $sheet->getParent()->addNamedRange( new NamedRange('departments',
            $sheet->getDelegate(), '$X$1:$X$' . sizeof($departments)) );

        $sheet->getParent()->addNamedRange( new NamedRange('pedagogicals',
            $sheet->getDelegate(), '$Y$1:$Y$' . sizeof($pedagogicals)) );

        $sheet->getParent()->addNamedRange( new NamedRange('ranks',
            $sheet->getDelegate(), '$Z$1:$Z$' . sizeof($ranks)) );
    }

    /**
     * @param Worksheet $sheet
     * @param DataValidation $validation
     * @throws \Exception
     */
    public function setRanges($sheet, $validation){
        for($i = 3; $i <= $this->countRows; $i++){
            $val = clone $validation;
            $val->setFormula1('scientifics');
            $sheet->getCell("I$i")->setDataValidation($val);

            $val = clone $validation;
            $val->setFormula1('academics');
            $sheet->getCell("K$i")->setDataValidation($val);

            $val = clone $validation;
            $val->setFormula1('commissions');
            $sheet->getCell("C$i")->setDataValidation($val);

            $val = clone $validation;
            $val->setFormula1('departments');
            $sheet->getCell("D$i")->setDataValidation($val);

            $val = clone $validation;
            $val->setFormula1('ranks');
            $sheet->getCell("E$i")->setDataValidation($val);

            $val = clone $validation;
            $val->setFormula1('pedagogicals');
            $sheet->getCell("F$i")->setDataValidation($val);
        }
    }

    /**
     * @param Worksheet $sheet
     * @return mixed
     * @throws \Exception
     */
    public function createValidation($sheet){
        $validation = $sheet->getCell('B1')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Value is not in list.');
        $validation->setPromptTitle('Pick from list');
        $validation->setPrompt('Please pick a value from the drop-down list.');

        return $validation;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
          AfterSheet::class => function(AfterSheet $event){
              /**
               * @var Worksheet $sheet
               */
              $sheet = $event->sheet;

              foreach (range('A', 'Z') as $col)
                $sheet->getColumnDimension($col)->setAutoSize(true);

              //get data for lists
              $this->createRanges($sheet);

              //create validation example
              $validation = $this->createValidation($sheet);
              $this->setRanges($sheet, $validation);
          }
        ];
    }
}
