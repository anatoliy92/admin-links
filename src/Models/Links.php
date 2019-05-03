<?php namespace Avl\AdminLinks\Models;

use App\Traits\ModelTrait;
use LaravelLocalization;
use App\Models\Media;

use Illuminate\Database\Eloquent\Model;

class Links extends Model
{
	use ModelTrait;

	protected $tables = 'links';

	protected $modelName = __CLASS__;

	protected $guarded = [];

	protected $lang = null;

	public function __construct ()
	{
		$this->lang = LaravelLocalization::getCurrentLocale();
	}

	public function media ($type = 'image')
	{
		return Media::whereModel('Avl\AdminLinks\Models\Links')->where('model_id', $this->id)->where('type', $type);
	}

	public function getTitleAttribute ($value, $lang = null) {
		$title = (!is_null($lang)) ? $lang : $this->lang;

		return ($this->{'title_' . $title}) ? $this->{'title_' . $title} : $this->title_ru ;
	}

	public function getDescriptionAttribute ($value, $lang = null) {
		$description = (!is_null($lang)) ? $lang : $this->lang;

		return ($this->{'description_' . $description}) ? $this->{'description_' . $description} : $this->description_ru ;
	}

	public function getLinkAttribute ($value, $lang = null) {
		$link = (!is_null($lang)) ? $lang : $this->lang;

		return ($this->{'link_' . $link}) ? $this->{'link_' . $link} : $this->link_ru ;
	}

}
