<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;
use Webpatser\Uuid\Uuid;

class Documento extends Model {
    use SoftDeletes, LaravelVueDatatableTrait;
    //nombre de la table --------------------------------------------------------->
    protected $table = 'documentos';
    //nombre de la table --------------------------------------------------------->
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'modulo',
        'modulo_id',
        'nombre_archivo',
        'nombre_carga',
        'url',
        'url_descarga',
        'extension',
        'tamano',
        'aplicacion',
        'ruta_carga',
        'usuario_creacion',
        'usuario_actualizacion',
    ];

    //Campos Para mostrar en Api, con filtro --------------------------------------------------------->

    protected $dataTableColumns = [
        'id' => ['searchable' => false],
        'modulo' => ['searchable' => false],
        'modulo_id' => ['searchable' => false],
        'nombre_archivo' => ['searchable' => false],
        'nombre_carga' => ['searchable' => false],
        'url' => ['searchable' => false],
        'url_descarga' => ['searchable' => false],
        'extension' => ['searchable' => false],
        'tamano' => ['searchable' => false],
        'aplicacion' => ['searchable' => false],
        'ruta_carga' => ['searchable' => false],
    ];

    //Campos Para mostrar en Api, con filtro --------------------------------------------------------->

    //Relaciones --------------------------------------------------------->

    //Relaciones --------------------------------------------------------->

    //Creacion del campo id Uuid --------------------------------------------------------->
    public static function boot() {
        parent::boot();
        self::creating(
            function ($model) {
                $model->id = (string) Uuid::generate(4);
            }
        );
    }

    protected $casts = [
        'id' => 'string',
    ];
    //Creacion del campo id Uuid --------------------------------------------------------->

}
