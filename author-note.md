# Author Note
This is just my personal note made public. The examples below are few possibilities with Moorexa.

### perform a deep scan for a file (directory, file)
```php
func()->deepscan('modules', 'Errors.php’);
```

### get an environment var
```php
var_dump(env('bootstrap', 'controller.base.path'));
```

### set an environment var
```php
var_dump(env_set('bootstrap/controller.base.path', FRAMEWORK_BASE_PATH));
```

### working with event
You can generate an event class with ```php assist new event <event name>```
```php
use Lightroom\Events\{
    Dispatcher, Listener, AttachEvent
};

// attach an event
AttachEvent::attach(Moorexa\Events\Mummy::class, 'mummy');

// listen for event
Listener::mummy('ready', function($name){

    var_dump('hello ' . $name);
    
});

// dispatch event
Dispatcher::mummy('ready', 'ifeanyi');
```

### working with request rules to filter and validate inputs
```php
use Lightroom\Requests\Rules\Input;

 public function home(Ap\Models\Give $giveModel, Input $input, $name) : void 
 {
    $rulesModel = $input->bindTo($giveModel)->useRule('filterRequest', $name);

    if ($rulesModel->isOk()) :

        // using pick
        $picked = $rulesModel->pick('username');

        if ($picked->model->exists($record)) :

            var_dump($picked->model->get());

        endif;

    endif;

    var_dump($rulesModel->getErrors());
    //$this->view->render('home');
 }
```

### create your own validator and apply to rules
```php
use Lightroom\Requests\Rules\Validator;

/**
 * @package Validator Filters
 */
class Filters extends Validator
{
    /**
     * @method Filters nostring
     * @param string $string
     * @return bool
     */
    public function nostring(string $string) : bool
    {
        // set error
        $this->setError('No strings are allowed'); 

        // return bool
        return is_string($string) ? false : true;
    }
}
```

### load Static files within a controller directory
where AP is the name of a controller
```php
use function Lightroom\Templates\Functions\{view};

// load controller js and css
view()->requireJs(AP_STATIC . 'Ap.js')->requireCss(AP_STATIC . 'Ap.css');
```

### session that re-validates and expires
```php
use function Lightroom\Requests\Functions\{session};

// set session
session()->set('name', 'chris', ['revalidate' => '2 minutes', 'expire' => '15 days']);
```

### Building a table scheme
sqlite database schema
```php
Table::create('users', function(){

    $this->integer('userid');
    $this->integer('group_id');
    $this->string('first_name');
    $this->string('last_name');
    $this->text('email')->unique();
    $this->text('phone')->unique();
    $this->primary('userid', 'group_id');
    $this->foreign('userid', function(){
        $this->references('contacts', 'contact_id', function(){
            $this->on('DELETE', 'CASCADE');
            $this->on('UPDATE', 'NO ACTION');
        });
    });
    $this->foreign('group_id', function(){
        $this->references('groups', 'group_id', function(){
            $this->on('DELETE', 'CASCADE');
            $this->on('UPDATE', 'NO ACTION');
        });
    });
});
```

### a preview to the default query builder
```php
var_dump(rows($query->table('users')->select('count(*)')));

$build = $builder->table('users')->get()->exists(function(){
            $this->table('orders')->get()->where('users.userid = orders.userid');
})->go();

$build = $builder->table('users')->update([
            'group_id' => function(){
                $this->table('orders')->get('users.group_id = ?', 'orders.group_id');
            }
        ])->exists(function(){
            $this->table('orders')->get()->where('users.userid = orders.userid');
});

$build = $builder->table('users')
        ->get('users.group_id, users.first_name')
        ->innerJoin('orders')->on('users.group_id = orders.group_id')
        ->where('userid', 2)->go();

$build = $builder->table('users')
        ->get('users.group_id, users.first_name')
        ->innerJoin('orders')->on('users.group_id = orders.group_id')
        ->where('userid', 2)->go();

$build = $builder->table('users')
        ->get('users.group_id, users.first_name')
        ->interset(function(){
            $this->table('groups')->get()->orderBy('group_id', 'desc');
})->go();

$build = $builder->table('users')
        ->get('users.group_id, users.first_name')
        ->union(function(){
            $this->table('groups')->get()->orderBy('group_id', 'desc');
})->go();

$build = $builder->table('users')->get()->between(100)->and(200);

$build = $builder->table('users')->get()->between("CAST('2014-02-01' AS DATE)")->and("CAST('2014-02-01' AS DATE)");

$build = $builder->table('users > c')->get()->where('c.userid IN', function(){
            $this->table('orders > r')->get('r.group_id')->where('r.orderid < 20');
})->go();

$build = $builder->table('users > c')->from([
            'subquery1' => function(){
                $this->table('orders > o')->get('c.userid = ?', 'o.userid');
            }
])->get()->where('subquery1.site_name = c.site_name')->go();

$build = $builder->table('users > p1')->get(['p1.full_name', function(){
            $this->table('orders > p2')->get('p1.group_id = p2.group_id')->setAs('subquery2');
}])->go();

$builder = driver('Mysql\Builder');

// create a table
driver('Mysql\Table::create', 'users', function(){

});

$users = map($builder->table('users')->get()->orderByPrimaryKey('desc'));

$users->obj(function($row){
    var_dump($row->primary());
});
```

### adding a channel to a database configuration 
```php
    'driver'    => Lightroom\Database\Drivers\Mysql\Driver::class,
    'host'      => 'localhost',
    'user'      => 'root',
    'pass'      => 'root',
    'dbname'    => 'testdb',
    'channel'   => Mysql\Channel::class
```

### loading additional template engines for a view
```php
use function Lightroom\Templates\Functions\{happy};

// load twig and latte
happy(function(){
    $this->extends('twig', 'latte');
});
```

### render a view and pass data to the view
```php
$this->view->render('home', [
    'name' => 'chris2,', 
    'items' => ['book','car'],
    'repo' => array(
        array('name' => "resque" ),
        array('name' => "hub" ),
        array('name' => "rip" ),
    )]);
```

### A basic route mapping. Will result to 'app/get-url' if 'get-url or getUrl' was sent
```php
Route::satisfy('get-url|getUrl', 'app/get-url');

// example 2. {target} would result to 'subscribe'
Route::satisfy('subscribe', 'app/contact/{target}');
```

### using app container to create, hold, reuse, functions and classes
showing several options.
```php
app()->call('mysql', 'cos', 1);
app('mysql')->cos(20);
app()->get('mysql', 'user');

var_dump(app()->set('mysql', 'user', 40));

app('res', 30);
app()->drop('res');
app()->add('res', Mysql::class);
app('res')->instance(20); // get instance 
app('res')->name = 20; // (static or non static property)
app('res')->status(); // (static or non static method)
app()->fresh('gateway'); // create a fresh instance

//add account
app()->add('account', App\Models\Account::class);
app()->add('account\App', App\Models\Account::class);

// add interface
app()->add('controllerInterface', \Lightroom\Packager\Moorexa\Interfaces\ControllerInterface::class);

// add trait
app()->add('AccountTrait', \Lightroom\Requests\Get::class);

var_dump(app()->all());

// it will create an alias of class 'App\Models\Account::class' as 'Account::class'
var_dump('Class exists ? ', class_exists(Account::class));

//var_dump(new Account\App);
var_dump(new Account);
```

### explore inner select query
```php
// run query
$query = $database->table($otherTable . ' > fT')->from(
[
    'cT' => function() use ($table, $column, $arguments, $primary)
    {
        // load primary key
        if (is_numeric($arguments[0])) $arguments[0] = 'mT.' . $primary . ' = ' . $arguments[0];

        // load table
        call_user_func_array([$this->table($table . ' > mT')->get($column), 'where'], $arguments);
    }

])->get()->whereString([
    'fT.' . $column => 'cT.' . $column
])->go();
```

### using light query for models
```php
use Lightroom\Database\LightQuery;

// where Account is a model

$this->setTable('users');
$this->setDatabase(‘new-db@test’);
var_dump($this->all(['username' => 'ifeanyi']));
var_dump(Account::all());
var_dump(Account::rows());
var_dump(Account::first());
var_dump(Account::last());
var_dump(Account::findBy(1));
var_dump(Account::findBy('username', 'chris'));
var_dump(Account::findBy('username', ['chris', 'ifeanyi'])); // and
var_dump(Account::findBy('username', 'chris', 'ifeanyi')); // or
var_dump(Account::add(['username' => 'chris3']));
var_dump(Account::updateLast(['username' => 'chris3']));
var_dump(Account::fromForeign(['test_accounts', 'accountsid'], 1));
var_dump(Account::update(['username' => 'chris3'], 1));
var_dump(Account::drop(14));
var_dump(Account::drop(15, function($row){
    var_dump($row);
}));
var_dump(Account::findRow('chris', 'ifeanyi'));
var_dump(Account::findLike('username', 'ifeanyi'));
$this->rows('email = ?', $input->email)
$this->update(['subscribe_to_mailchip' => 1 ], $input->pick('email'));
```

### using filter function for safe inputs
```php
$input = filter(['name' => $name], [
    'name' => 'string|notag|min:1|max:2|required'
]);

$input->name = 'chris';

$input = filter('post', ['name' => 'string|notag']);
$input = filter('get', ['name' => 'string|notag']);
$input = filter('header', ['name' => 'string|notag']);
$input = filter(Validator::class, ['name' => 'chris'],  ['name' => 'string|notag']);
$input = filter(Validator::class, 'post',  ['name' => 'string|notag']);
$input = filter(Validator::class, 'get',  ['name' => 'string|notag']);
$input = filter(Validator::class, 'header',  ['name' => 'string|notag']);

var_dump($input->filter());
var_dump($input->name);
var_dump($input->isOk()); 
var_dump($input->json());
var_dump($input->data());
var_dump($input->object());
var_dump($input->get('name'));
var_dump($input->set('name', 'chris'));
var_dump($input->set('name', 'chris')->filter());
var_dump($input->set('name', 'chris')->filter([
    'name' => 'string|max:20'
]));
var_dump($input->getErrors());
var_dump($input->getError('name'));
```

### show CSRF error in a view file, using partials.
```html
In view

@csrf-error;
```

### handling requests in a model class
```php
// In model

$this->request('post');
$this->request('get');
$this->request('header');
$this->viewVar('name', 'chris');
```

### inserting data with nested select statements
```php
use function Lightroom\Database\Functions\{db};

var_dump(db('routes')->insert([
        'routeid' => function($db){
            $db->table('users > u')->get('userid')->where(['u.userid' => 2]);
        }
    ])->go());

$user = db('routes')->get(['routeid', function(){
        $this->table('orders > p2')->get('p1.group_id = p2.group_id')->setAs('subquery2');
    }], ['routeid' => 2])->go();

```

### using active records with identity
```php
var_dump($route->from('users', 'routeid')->get('routeid, route'));
```

### using HYPHE, reusable html directive. Inspired by React JSX
You can pass props, get child's and much more.
```html
<hy>
    <element name="chris"/>
</hy>

<hy-element name="chris">hello</hy-element> 

<hy-form button="submit">
    <div class="form-group">
        <hy-input name="fullname" type="text">Your Full Name</hy-input> 
        <hy-input name="email" type="email">Email Address</hy-input> 
        <hy-input name="subject" type="text">subject</hy-input> 
    </div>
</hy-form>
```

### clear all post entries or get the last entry
```php
// clear all
$post->clear();

// clear message
$post->pop('message');
```

## working with redirection
Send data and retrieve them
```php
use function Lightroom\Templates\Functions\{redirect};

// will access exported data once because of multiple instances
$data = redirect()->data();
redirect()->has('name', 'age');
redirect()->get('name');
redirect()->name;

// best way to go about this
$redirect = redirect(); // will create only one instance

// now you can do the checking
$redirect->data();
$redirect->has('name', 'age');
$redirect->get('name');
$redirect->name;

// how redirect works
redirect('admin/user');
// export data
redirect('admin/user', ['name' => 'chris']);
// add query
redirect('admin/user', [
    'query' => [
        'id' => 2
    ]
]);
// add query and data
redirect('admin/user', [
    'query' => [
        'id' => 2
    ],
    'data' => [
        'name' => 'chris'
    ]
]);
// export external
redirect('http://www.example.com');

// add query
redirect('http://www.example.com', ['id' => 2]);
```

### using the fetch component in your view for simple get queries
```html
@fetch(['table' => 'admin', 'where' => ['adminid' => 2], 'orderBy' => ['adminid', 'asc']], 'row')

@endfetch


@fetch(‘admin’, 'row')

@endfetch
```

### listening to routes and request methods in models with actionables
```php
/**
 * @var array $actionable
*/
public $actionable = [
    'events/delete' => [
        'GET' => 'getEventsDelete'
    ],
    'delete' => ['GET' => '']
];
```
### subscribing to assets events
```php
use Lightroom\Packager\Moorexa\Helpers\Assets;

Assets::subscribe('css', function(string $file){

});

Assets::subscribe('js', function(string $file){

});

Assets::subscribe('image', function(string $file){

});

Assets::subscribe('media', function(string $file){

});

Assets::subscribe('loadJs', function(string $file){

});

Assets::subscribe('loadCss', function(string $file){

});
```

### use controller config file as default
```php
// use configuration as default
'use.default' => true
```

### using the built in unit test for routes
```php
$app = $this->request('get', 'app/home');

$this->should(['return', 200], $app->code);

$app = $this->request('get', 'app/view-project-3');
$this->should(['return', 301], $app->code);

$this->should('redirect_user', $app);
```

### using a method in a model for processing a submitted form
This should be used in an html view form
```html
@method('@putContact'); // load method putContact from model.
```

Request-Session-Token (This would be sent by the server when accessing a page that uses a token without a user agent.)

### working with states
```php
state()->register([
    'callback' => \helpers\Manager::class,
    'caller' => 'manager',
    'data' => ['expertiseid' => 2],
    'table' => 'expertise'
]);

state('manager')->remove();
state()->remove();
state()->refresh();
state()->my_expertise;
state()->insertNavigation()->nav_name;
```

### loading async images
```html
$async=“image path or name”
$background-async=“image path or name”
```

### applying middlewares to control boot payloads
```json
{
    "name": "Moorexa Middleware for payloads",
    "description": "Let's help you manage the payloads with middlewares. You may have to generate the middleware from the cli.",
    "payloads" : [
        {"load-requirement":"Moorexa\\Middlewares\\Man, Moorexa\\Middlewares\\Man2"},
        {"load-dependency-checker":""},
        {"load-global-variables":""},
        {"load-config":""},
        {"load-security-group":""},
        {"load-request-manager":""},
        {"load-database-handler":""},
        {"script-processor":""},
        {"load-router":""},
        {"load-csrf-manager":""},
        {"load-view-templates":""},
        {"attach-middleware":""},
        {"attach-view":""}
    ]
}
```

### manage route callback with a class 
```php
use Lightroom\Packager\Moorexa\Router as Route;

Route::get('route-name', ['class name', 'class method']);
```

### create reusable closure
```php
use Lightroom\Packager\Moorexa\Router as Route;

Route::createFunc('verify-mail', function($activation_code){
});
```

### working with the event helper function
```php
event('ev', function(){
    $this->on('footer.ready', function($data){
        var_dump($data);
    });

	$this->emit('footer.ready', 200);
});

// listen for model.ready event
event('ev')->on('model.ready', function($data){
    
});

event()->emit('ev', 'footer.ready', []);
event()->on('ev', 'footer.ready', function(...$args){ });
event()->attach(MyClass::class, 'alias');
event()->canEmit('eventClass.event') : bool

```

### events available on ‘ev’
1. view.ready
2. header.ready
3. footer.ready
4. controller.ready
5. view.load
6. model.load
7. controller.provider.ready
8. view.provider.ready
9. model.ready
10. redirection
11. view.action.ready
12. partial.ready
13. view.getPath
14. 
15. 

### working with the built in JSON DB, also call FILEDB
```php
$menu = fdb()->get('menu.driver.public', function($db){
            //$db->limit(0,3);
            $db->where(':title = ? or :title = ?', 'Cities');
        });

//var_dump($menu->first());
//var_dump($menu->last());
//var_dump($menu->index(2));

if ($menu->rows > 0) :

    while ($row = $menu->fetch()) :

        // get row
        var_dump($row);

    endwhile;

endif;
```

### applying middlewares using 'apply'. 
```php
use Lightroom\Packager\Moorexa\Router as Route;

// apply middleware
$ready = func()->trueOnly([
    Middlewares::apply(Moorexa\Middlewares\Access::class, ['app', 'home'])
]);


Route::any('*', function(array $request){

    // apply middleware
    $ready = func()->trueOnly([
        Middlewares::apply(Moorexa\Middlewares\Access::class, $request)
    ]);

    // render
    return ($ready) ? implode('/', $request) : '';

});

// toggle next and prev routes
Route::next('render');
Route::prev('render');
```

### More filtering rules
```php
'case_images' => 'required|file|file_multiple|filetype:jpg,jpeg,png,gif'

'audio' => 'required|file|filetype:aiff,mp3,wav,aac,ogg,wma,flac,alac,wma,m4a,audio/mp4'
```

### using route resolvers with middlewares
```php
use Lightroom\Packager\Moorexa\Router as Route;

// approve a volunteer
Route::resolvePost('/approve/{accountid}', ['accountid' => '[0-9]+'], function($accountid)
{
    return 'records/approve/' . $accountid;
},
// resolver
function($callback, $request)
{
    // apply middleware
    $allow = Lightroom\Router\Middlewares::apply(Moorexa\Middlewares\HasRights::class, $request);

    // we good ?
    if ($allow) $callback();

});

Route::resolvePost
Route::resolveGet
Route::resolvePut
Route::resolveDelete
Route::resolveAny
Etc..
```

### using route resource
```php

    use Lightroom\Packager\Moorexa\RouterMethods;
    use Lightroom\Packager\Moorexa\Interfaces\ResourceInterface;

    /**
     * @package download route class
     * @author Amadi Ifeanyi
     */
    class DownloadRoute implements ResourceInterface
    {
        /**
         * @method ResourceInterface onRequest
         * @param RouterMethods $method
         * @return void
         * 
         * Here is a basic example of how this works.
         * $method->get('hello/{name}', 'methodName');
         * 
         * Where "methodName" is a public method within class.
         * Hope it's simple enough?
         */
        public function onRequest(RouterMethods $method) : void
        {
            $method->get('/installation-complete/{id}', 'installationComplete');
        }

        /**
         * @method DownloadRoute installationComplete
         * @param int $id
         * Installation has been completed
         */
        public function installationComplete(int $id)
        {
            // your code here... 
        }
    }

    // load resource for download and installation
    Route::resource(DownloadRoute::class);
```

To set content type with request
Add this to your request header

```php
Set-Content-Type : 'content type'
```

### export variables to a partial file globally
```php
use Lightroom\Packager\Moorexa\Helpers\Partials;

Partials::exportVars('alert-modal', [
        'alertType'     => ($type == 'success') ? 'auto-show alert-success' : 'auto-show',
        'alertMessage'  => $message
]);
```

Actually, this is just a tip of what's possible with Moorexa. We are working on the full documentation, please visit [http://www.moorexa.com] and subscribe to our release broadcast. Thank you!