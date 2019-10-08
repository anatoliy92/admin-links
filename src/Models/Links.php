<?php namespace Avl\AdminLinks\Models;

use App\Traits\ModelTrait;
use LaravelLocalization;
use App\Models\Media;

use Illuminate\Database\Eloquent\Model;

class Links extends Model
{
	use ModelTrait;

	protected $table = 'links';

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

	public function rubric ()
    {
	    return $this->belongsTo(\App\Models\Rubrics::class, 'rubric_id', 'id');
    }

	public function getTitleAttribute ($value, $lang = null) {
		$title = (!is_null($lang)) ? $lang : $this->lang;

		return ($this->{'title_' . $title}) ? $this->{'title_' . $title} : null;
	}

	public function getDescriptionAttribute ($value, $lang = null) {
		$description = (!is_null($lang)) ? $lang : $this->lang;

		return ($this->{'description_' . $description}) ? $this->{'description_' . $description} : $this->description_ru ;
	}

	public function getLinkAttribute ($value, $lang = null) {
		$link = (!is_null($lang)) ? $lang : $this->lang;

		return ($this->{'link_' . $link}) ? $this->{'link_' . $link} : $this->link_ru ;
	}

	public function getBlankAttribute ()
	{
		return ((substr($this->link, 0, 7) == 'http://') || (substr($this->link, 0, 8) == 'https://')) ? 'target="_blank"' : 'target="_self"';
	}

}
