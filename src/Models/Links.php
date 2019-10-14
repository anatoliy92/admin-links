<?php namespace Avl\AdminLinks\Models;

use Darmen\Moderation\Models\Moderatable;
use App\Traits\ModelTrait;
use LaravelLocalization;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;


class Links extends Model implements Moderatable
{
	use ModelTrait;

	protected $table = 'links';

	protected $modelName = __CLASS__;

	protected $guarded = [];

	protected $lang = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->lang = LaravelLocalization::getCurrentLocale();
    }

    public function section()
    {
        return $this->belongsTo('App\Models\Sections', 'section_id', 'id');
    }

	public function media ($type = 'image')
	{
		return Media::whereModel('Avl\AdminLinks\Models\Links')->where('model_id', $this->id)->where('type', $type);
	}

	public function image ()
	{
		return $this->media('image')->where(function ($query) {
		    $query->whereLang($this->lang);
        })->first();
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

		return ($this->{'description_' . $description}) ? $this->{'description_' . $description} : null ;
	}

	public function getLinkAttribute ($value, $lang = null) {
		$link = (!is_null($lang)) ? $lang : $this->lang;

		return ($this->{'link_' . $link}) ? $this->{'link_' . $link} : null ;
	}

	public function getBlankAttribute ()
	{
		return ((substr($this->link, 0, 7) == 'http://') || (substr($this->link, 0, 8) == 'https://')) ? 'target="_blank"' : 'target="_self"';
	}

    public function getId()
    {
        return $this->getKey();
    }

    public function getEditUrl()
    {
        return '/sections/' . $this->section_id . '/links/' . $this->id;
    }

    public function getModerationModelName()
    {
        $section = $this->section;

        return (($section) ? $section->name_ru . ' - ' : '') . $this->title_ru ?? 'Каталог ссылок';
    }

    public function publish()
    {
        $this->setAttribute('good_kz', true);
        $this->setAttribute('good_ru', true);
        $this->setAttribute('good_en', true);
    }

    public function unPublish()
    {
        $this->setAttribute('good_kz', false);
        $this->setAttribute('good_ru', false);
        $this->setAttribute('good_en', false);
    }

}
