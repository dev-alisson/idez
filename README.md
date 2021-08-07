## About
This is a fictitious project used to fulfill the iDez company's <br/>
challenge to compete for a back-end developer position.

#### - Versions
-- Laravel v8.* <br/>
-- PHP v7.3.*

#### - Development
-- routes/api.php <br/>
-- app/Http/Controllers/UserController.php <br/>
-- app/Http/Controllers/AccountController.php <br/>
-- app/Http/Controllers/DepositController.php <br/>
-- app/Http/Controllers/TransferController.php

#### Commands

-- php artisan migrate <br/>
-- php artisan serve

#### - Rroutes

##### - Users

[GET] /api/users <br/>
[POST] /api/users <br/>
[GET] /api/users/{id} <br/>
[PUT] /api/users/{id} <br/>
[DELETE] /api/users/{id} <br/> <br/>

{ <br/>
	"name": "Nome", <br/>
	"lastname": "Sobrenome", <br/>
	"document": "111.222.333-44", <br/>
	"phone": "(11) 22222-3333", <br/>
	"email": "email@gmail.com", <br/>
	"password": "123456" <br/>
}

##### - Accounts

[GET] /api/accounts <br/>
[POST] /api/accounts <br/>
[GET] /api/accounts/{id} <br/>
[PUT] /api/accounts/{id} <br/>
[DELETE] /api/accounts/{id} <br/> <br/>

{ <br/>
	"user_id": "1", <br/>
	"agency": "1111", <br/>
	"number": "22222", <br/>
	"digit": "3", <br/>
	"cnpj": null, <br/>
	"corporate_name": null, <br/>
	"fantasy_name": null, <br/>
	"type": "person" <br/>
}, <br/>
{ <br/>
	"user_id": "1", <br/>
	"agency": "1111", <br/>
	"number": "22222", <br/>
	"digit": "3", <br/>
	"cnpj": "11.222.333/4444-55", <br/>
	"corporate_name": "Name Company Corporation LTDA", <br/>
	"fantasy_name": "Company", <br/>
	"type": "company" <br/>
}

##### - Deposits

[GET] /api/deposits <br/>
[POST] /api/deposits <br/>
[GET] /api/deposits/{id} <br/> <br/>

{ <br/>
	"account_id": "4", <br/>
	"amount": "400" <br/>
}

##### - Transfers

[GET] /api/transfers <br/>
[POST] /api/transfers <br/>
[GET] /api/transfers/{id} <br/> <br/>

{ <br/>
	"shipping_account_id": "1", <br/>
	"receiving_account_id": "2", <br/>
	"amount": "100" <br/>
}
