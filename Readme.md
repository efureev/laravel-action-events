# Event log system

## Information

This package allows you to store various event actions: model changes, events, just strings

### Features

- Keep `original` & `change` data
- Keep `Extra` data
- Store various value types: `String`, `Event`, `Model`
- Has various event types:
    - `Change` For change data Actions (Models: create/update/delete)
    - `Event` For any Events in system (Login success/failed)
    - `Read` For read data (Export data, etc)
- Store an event with a model in one transaction
- Has relations (through trait: `HasActions`) to the Eloquent Models: `$model->actions`
- Has relation (through trait: `MadeActions`) to author user: `$user->madeActions`
- Has statuses: `Done`, `Running`, `Failed`

## Examples

Log event from `String`

```php
app('actionEvents')->push('custom event');
```

Log event from Laravel `Event` with interface `Actionable`

```php
class Login implements Actionable
{
    public function getName(): string
    {
        return 'login';
    }
}

// ...

app('actionEvents')->push(new Login());
```

Log event from Laravel `Event` wo interface `Actionable`

```php
class CustomEvent
{
}

// ...

app('actionEvents')->push(new CustomEvent());
```

Log event from special class `ActionEvent` or other which implementing interface `ActionEventable`

```php
app('actionEvents')->push(new ActionEvent('event'));
app('actionEvents')->push(ActionEvent::make('event')->typeRead());
app('actionEvents')->push(new ActionEvent('event', ActionEventType::READ));
```

Log data change from Laravel Model: create

```php
$attributes = [
// ...
];
$dataModel = User::create($attributes);
$modelEvent = app('actionEvents')->pushByModelCreate($dataModel);
```

Log data change from Laravel Model: update

```php
$attributes1 = [
// ...
];
$dataModel = User::create([]);
$dataModel->fill(['name'=>'test']);

$modelEvent = app('actionEvents')->pushByModelUpdate($dataModel);
$dataModel->save();
```

or

```php
$attributes1 = [
// ...
];
$dataModel = User::create([]);
$dataModel->fill(['name'=>'test']);
$changes = $dataModel->getDirty();
$dataModel->save();

$modelEvent = app('actionEvents')->pushByModelUpdate($dataModel, $changes);
```

Log data change from Laravel Model with transaction: create

```php
$attributes1 = [
// ...
];
$dataModel = User::create([]);
$modelEvent = app('actionEvents')->pushAndSaveByModelCreate($dataModel);
```

Log data change from Laravel Model with transaction: update

```php
$attributes1 = [
// ...
];
$dataModel = User::create([]);
$dataModel->fill(['name'=>'test']);
$modelEvent = app('actionEvents')->pushAndSaveByModelUpdate($dataModel);
```

Log collections

```php
$staffModels = new Collection([Model::create(),'test', new Login(),Model::create()]);
$models = app('actionEvents')->pushByCollectionCreate($staffModels);
```

### Roadmap

- Work with Collections
