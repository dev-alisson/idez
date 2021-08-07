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

-- php artisan migrate
-- php artisan serve

#### - Rroutes

##### - Users

[GET] /api/users
[POST] /api/users
[GET] /api/users/{id}
[PUT] /api/users/{id}
[DELETE] /api/users/{id}

{
	"name": "Nome",
	"lastname": "Sobrenome",
	"document": "111.222.333-44",
	"phone": "(11) 22222-3333",
	"email": "email@gmail.com",
	"password": "123456"
}

##### - Accounts

[GET] /api/accounts
[POST] /api/accounts
[GET] /api/accounts/{id}
[PUT] /api/accounts/{id}
[DELETE] /api/accounts/{id}

{
	"user_id": "1",
	"agency": "1111",
	"number": "22222",
	"digit": "3",
	"cnpj": null,
	"corporate_name": null,
	"fantasy_name": null,
	"type": "person"
},
{
	"user_id": "1",
	"agency": "1111",
	"number": "22222",
	"digit": "3",
	"cnpj": "11.222.333/4444-55",
	"corporate_name": "Name Company Corporation LTDA",
	"fantasy_name": "Company",
	"type": "company"
}

##### - Deposits

[GET] /api/deposits
[POST] /api/deposits
[GET] /api/deposits/{id}

{
	"account_id": "4",
	"amount": "400"
}

##### - Transfers

[GET] /api/transfers
[POST] /api/transfers
[GET] /api/transfers/{id}

{
	"shipping_account_id": "1",
	"receiving_account_id": "2",
	"amount": "100"
}
