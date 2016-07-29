<?php

namespace Chriscalifornia88\RestEasy;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class RestController extends Controller
{
    const MODEL = '';

    const VALIDATION_CREATE = [];
    const VALIDATION_UPDATE = [];

    const CRUD_ROUTES = [
        'only' => [
            'index',
            'store',
            'show',
            'update',
            'destroy'
        ]
    ];

    public function __construct($except = [])
    {
        // Require login
        $this->middleware('auth')->except($except);
    }

    /**
     * Break the model's namespace into a chain of models
     * @return array
     */
    protected static function getModelChain()
    {
        $shortClassname = str_replace('App\\Models\\', '', static::MODEL);
        $classes = explode('\\', $shortClassname);

        return $classes;
    }

    /**
     * Instantiate the model chain
     * @param int[] $ids
     * @param bool $stopAtLastParent if true, the final model in the chain does not get instantiated
     * @return \Eloquent
     */
    protected static function instantiateModelChain($ids, $stopAtLastParent = false)
    {
        // Remove Request objects
        $ids = array_filter(
            $ids,
            function ($id) {
                return !is_object($id);
            }
        );
        $ids = array_values($ids);

        $models = static::getModelChain();
        
        if($stopAtLastParent) {
            array_pop($models);
        }

        /** @var \Eloquent $instance */
        $instance = null;
        foreach ($models as $index => $model) {
            $id = null;
            if (isset($ids[$index])) {
                $id = $ids[$index];
            }

            if (is_null($instance)) {
                /** @var \Eloquent $class */
                $class = "App\\Models\\$model";
                if (!is_null($id)) {
                    // Instantiate the top parent model
                    $instance = $class::find($id);

                    if (is_null($instance)) {
                        throw (new ModelNotFoundException())->setModel($model);
                    }
                } else {
                    $instance = $class;
                }
            } else {
                // Instantiate the child relationship
                $relationship = str_plural(lcfirst($model));
                if (!is_null($id)) {
                    $instance = $instance->$relationship()->where('id', $id)->first();
                    if (is_null($instance)) {
                        throw (new ModelNotFoundException())->setModel($model);
                    }
                } else {
                    $instance = $instance->$relationship();
                }
            }
        }

        return $instance;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = static::instantiateModelChain(func_get_args());

        if (!is_object($model)) {
            $models = $model::all();
        } else {
            $models = $model->get();
        }

        return response()->json(['data' => $models]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, static::VALIDATION_CREATE);

        $model = static::instantiateModelChain(func_get_args());

        /** @var \Eloquent $instance */
        if (!is_object($model)) {
            $instance = $model::create($request->all());
        } else {
            $instance = $model->create($request->all());
        }
        $instance->save();

        return response()->json(['data' => $instance->attributesToArray()]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $instance = static::instantiateModelChain(func_get_args());

        return response()->json(['data' => $instance]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, static::VALIDATION_UPDATE);

        $instance = static::instantiateModelChain(func_get_args());
        $instance->fill($request->all())->save();

        return response()->json(['data' => $instance->attributesToArray()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $instance = static::instantiateModelChain(func_get_args());
        $instance->delete();
    }
}
