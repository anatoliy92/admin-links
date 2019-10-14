<?php namespace Avl\AdminLinks\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Site\Sections\SectionsController;
use App\Models\Sections;
use View;

class LinksController extends SectionsController
{

	public function index (Request $request)
	{
        $template = 'site.templates.links.short.' . $this->getTemplateFileName($this->section->current_template->file_short);

        $records = $this->section->links()
                                ->where('good_' . $this->lang, 1)
                                ->orderBy('published_at', 'DESC')
                                ->paginate($this->section->current_template->records);

        $template = (View::exists($template)) ? $template : 'site.templates.links.short.default';

        return view($template, [
            'records' => $records,
            'pagination' => $records->appends($_GET)->links('vendor.pagination.default')
        ]);
	}
}
