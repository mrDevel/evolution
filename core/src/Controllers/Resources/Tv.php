<?php namespace EvolutionCMS\Controllers\Resources;

use EvolutionCMS\Models;
use EvolutionCMS\Controllers\AbstractResources;
use EvolutionCMS\Interfaces\ManagerTheme\TabControllerInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent;

//'actions'=>array('edit'=>array(301,'edit_template'), 'duplicate'=>array(304,'edit_template'), 'remove'=>array(303,'edit_template')),
class Tv extends AbstractResources implements TabControllerInterface
{
    protected $view = 'page.resources.tv';

    /**
     * {@inheritdoc}
     */
    public function getTabName($withIndex = true): string
    {
        return 'tabVariables' . ($withIndex ? '-' . $this->getIndex() : '');
    }

    /**
     * {@inheritdoc}
     */
    public function canView(): bool
    {
        return $this->managerTheme->getCore()->hasAnyPermissions([
            'new_template',
            'edit_template'
        ]);
    }

    protected function getBaseParams()
    {
        return array_merge(
            parent::getParameters(),
            [
                'tabPageName' => $this->getTabName(false),
                'tabIndexPageName' => $this->getTabName()
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(array $params = []) : array
    {
        $params = array_merge($this->getBaseParams(), $params);

        return $this->isNoData() ? $params : array_merge([
            'categories' => $this->parameterCategories(),
            'outCategory' => $this->parameterOutCategory()
        ], $params);
    }

    protected function parameterOutCategory(): Collection
    {
        return Models\SiteTmplvar::with('templates')
            ->where('category', '=', 0)
            ->orderBy('name', 'ASC')
            ->lockedView()
            ->get();
    }

    protected function parameterCategories(): Collection
    {
        return Models\Category::with('tvs.templates')
            ->whereHas('tvs', function (Eloquent\Builder $builder) {
                return $builder->lockedView();
            })->orderBy('rank', 'ASC')
            ->get();
    }
}
