<?php


namespace App\Repositories;


use App\Models\Qualification;
use App\Repositories\Interfaces\QualificationRepositoryInterface;
use App\Repositories\Rules\DateLessRule;
use App\Repositories\Rules\DateMoreRule;
use App\Repositories\Rules\EqualRule;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class QualificationRepository extends BaseRepository implements QualificationRepositoryInterface
{
    private $model = Qualification::class;
    private $sortField = [
        'ID' => 'id',
        'category' => 'name',
        'date' => 'date'
    ];

    public function createRules(array $inputData): array
    {
        $rules = [];

        if($inputData['user_id'] ?? null)
            $rules[] = new EqualRule('user_id', $inputData['user_id']);

        if($inputData['filterFrom'] ?? null)
            $rules[] = new DateMoreRule('date', $inputData['filterFrom']);

        if($inputData['filterTo'] ?? null)
            $rules[] = new DateLessRule('date', $inputData['filterTo']);

        if($inputData['filterCategory'] ?? null && $inputData['filterCategory'] != -1)
            $rules[] = new EqualRule('name', $inputData['filterCategory']);

        $rules = array_merge($rules, $this->createSortRules($inputData['sort'] ?? null, $this->sortField));
        return $rules;
    }

    public function getModel(): Model
    {
        return app($this->model);
    }

    public function all(): Collection
    {
        return $this->getModel()->all();
    }

    public function paginateForUser($user_id, ?int $size = null): LengthAwarePaginator
    {
        $size = $size ?? config('app.PAGINATE_SIZE', 10);
        return $this->getModel()->query()->where('user_id', $user_id)->paginate($size);
    }

    public function getLastQualificationDateOf(int $user_id)
    {
        $last = $this->getLastQualificationOf($user_id);

        if(!$last)
            return;

        return $last->date ?? null;
    }

    public function getNextQualificationDateOf(int $user_id)
    {
        $lastDate = $this->getLastQualificationDateOf($user_id);

        if(!$lastDate)
            return null;

        return Carbon::createFromFormat('Y-m-d', $lastDate)
                ->addYears(5)->format('Y-m-d');
    }

    public function getQualificationNameOf(int $user_id)
    {
        $name = $this->getModel()->query()->where('user_id', $user_id)
            ->orderBy('date', 'desc')->pluck('name')->first();

        return $name ?? null;
    }

    public function getLastQualificationOf(int $user_id){
        return $this->getModel()->query()->where('user_id', $user_id)
            ->orderBy('date', 'desc')->first();
    }
}
