Resto
=====

Resto is a Laravel Eloquent inspired ORM for REST like webservices, under the hood it uses Guzzle.

With Resto you can,
1. Handle unlimited # of Rest APIs within the project.
2. Reuse already built ones in another project.
3. Customize resto to handle majority of APIs out there.

##Quick Start

Consider an API of XYZ.com which has, Posts, Users. User has many posts. The API use a key to validate every request.

####Setup a project

Resto uses namesapce based convension to manage different APIs, this allows better code management and multiple API handling.

	app/
		XYZ/
			User.php
			Post.php

User.php

	namespace XYZ;

	class User extends Resto\Entity\Model
	{
		public function posts()
		{
			//no need to put the namespace
			return $this->hasMany('Post');
		}
	}

Post.php

	namespace XYZ;

	class Post extends Resto\Entity\Model
	{
		public function user()
		{
			//no need to put the namespace
			return $this->belongsTo('Post');
		}
	}

Before start using, we need to register this module as a Resto module.

	$module = Resto\Common\Module::register('XYZ');

Module class is a container for APIs, all the configuration data for a specific API will be stored under module class. Modules are identified/registered by their namespace. Once a module is registered, we can access the Guzzle request objects prior to execution.

####Setting up API endpoint
    $module = Resto\Common\Module::resolve('XYZ');
    $module->setEndPoint('http://api.xyz.com/v2');

####Setting up options of http transporter

"initiateHttpClient" is a callaback that's executed before each request of a specific module
	
	$module = Resto\Common\Module::resolve('XYZ');
	$module->setCallback('initiateHttpClient', function($request, $client){
		
		//append json ext to every request url
		$request->setPathExt('json');

		//guzzle http client
		$client->setDefaultOption('query', array('key' => 'password'));

		//http auth
		$client->setAuth('user', 'pass');
	});

Include these configuration setup inside a bootstrap file and include in startup of your app.

####Using the models

	XYZ\User::all(); //GET http://api.xyz.com/v2/users.json

	$user = XYZ\User::find(1); //GET http://api.xyz.com/v2/users/1.json
	$user->email = 'john@doe.com';
	$user->save(); //PUT http://api.xyz.com/v2/users/1.json, changed email will be sent as a post fields.

	$posts = $user->posts(); //GET http://api.xyz.com/v2/users/1/posts.json
	
	//query
	XYZ\User::query()->where('email', 'john@doe.com')->get(); //GET http://api.xyz.com/v2/users.json?email=john@doe.com


##Introduction to each component

*Modules*
http://d.pr/i/fYSm
To make it possible to manage few different APIs at once, Resto uses modules. Each API and it's models should be created under a namespaced directory and Resto will use the namespace to identify each module. Once a module is registered, it can be accessed at anytime to set configuration options such as API endpoint, API auth options etc.

*Query*
Query class generate requests and execute them. Query class is the middle man between API requests and Models.

*Request*
Under the hood, Query class uses a Request class to build up each request. Request class is the main transporter. Query class is tighly coupled with a specific Module while Request class isn't.

*Parsers*
Resto uses parsers to format data before it leaves the app and after response come.

Request Parser
When a new query is being executed, it runs through a request parser before execution. Request parser receives the Query object with Request object. Parser then modify the Request object accordingly. Say when doing a PUT request your API needs a xml body, you can create a request parser to do this.

Response Parser
Like the request parsers, Query object runs a response that comes after a request through a response. After each request query class needs data in an array and reponse parsers make sure that Query class receives what he needs.

By default Resto works with JSON outputs and for POST\PUT post fields are used. However you can create your own parsers to work with XML, or any other specific format and register it under a specific module.

*Relations*
Models can have relations with other models.

*Errors*
The default response parser check the response body for "errors" key and create `Resto\Exception\ResponseErrorException`s.

============

###Module ``Resto\Common\Module``

http://d.pr/i/fYSm

Before using set of models under an API, it should be registered as a module. Resto takes a namespace approach for this, so all your models should be inside a namespaced directory. Namespace will be used to identify the specific module. Make sure your app can autoload these models under the given namespace.

Thanks to module approach, you can handle few APIs using Resto.

	app/
		ZenDesk/
			Ticket.php
			User.php
			Group.php
		
		BaseCamp/
			Account.php
			Projectphp
			People.php

Resto\Common\Module::register('ZenDesk');
Resto\Common\Module::register('Basecamp');

#### Configuration options
Once a module is registered, you can start setting up configurations, callbacks, register different classes for Parsers (covered below).


###Request ``Resto\Common\Module``

Response
--------

Query
-----

Model
-----

Collection
----------

Relations
---------

Parsers
-------

