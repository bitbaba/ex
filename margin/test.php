<?php
function RPC_HOST()
{
	return "127.0.0.1";
}

function RPC_PORT()
{
	return "10086";
}

function GetParam($name){
	if (isset($_REQUEST[$name])) 
        return $_REQUEST[$name];
}

function TokenCheck(){
	return true;//(GetParam("token") == "secure");
}

function req($method, $params){
        $url = 'http://' . RPC_HOST() . ':'. RPC_PORT() .'/';
        $header[] = "content-type: text/plain";
        $json_req = '{"jsonrpc":"1.0", "id":"qm", "method":"'.$method.'", "params":'.$params.'}';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_req);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
}

function toaccount($token){
    return $token;
}

/**
* Database operations
*/
function DB_ListOrderBooks(){
	return req("DB_ListOrderBooks", '[]');
}

/**
* Remove Orderbook 
*/
function API_RemoveOrderBook($symbol){
	return req("API_RemoveOrderBook", '["'.$symbol.'"]');
}

/**
* Rename Orderbook 
*/
function API_RenameOrderBook($symbol, $display){
	return req("API_RenameOrderBook", '["'.$symbol.'","'.$display.'"]');
}

/**
* Rename Orderbook 
*/
function API_CreateOrderBook($symbol, $pair, $display, $liquitime){
	return req("API_CreateOrderBook", '["'.$symbol.'","'.$pair.'","'.$display.'",'.$liquitime.']');
}

/**
* Exported APIs
*/
function API_PlaceOrder($account_id, $symbol, $buy, $price, $qty, $leverage, $ref_contract_id){
	return req("API_PlaceOrder", '['.$account_id.', "'.$symbol.'", '.$buy.', "'.$price.'", "'.$qty.'", "'.$leverage.'", '.$ref_contract_id.']');
}

function API_RevokeOrder($account_id, $symbol, $order_id){
	return req("API_RevokeOrder", '['.$account_id.',"'.$symbol.'",'.$order_id.']');
}

function API_ListOrders($account_id, $filters){
	return req("API_ListOrders", '['.$account_id.',['.$filters.']]');
}

function API_ListDepth($symbol){
	return req("API_ListDepth", '["'.$symbol.'"]');
}

function API_ListContracts($account_id){
	return req("API_ListContracts", '['.$account_id.']');
}

function API_AddMargin($account_id, $symbol, $ref_contract_id, $margin){
    return req("API_AddMargin", '['.$account_id.',"'.$symbol.'",'.$ref_contract_id.',"'.$margin.'"]');
}

function API_GetBalance($account_id){	
	return req("API_GetBalance", '['.$account_id.']');
}

function API_Deposit($account_id, $coin, $qty){
	return req("API_Deposit", '['.$account_id.',"'.$coin.'","'.$qty.'"]');
}

function API_Withdrawal($account_id, $coin, $qty){
	return req("API_Withdrawal", '['.$account_id.',"'.$coin.'","'.$qty.'"]');
}

/**
* Main Entry
*/
function MainEntrance(){
	if (!TokenCheck()){ 
		return "auth failed";
	}

	$api=GetParam("api");
	/*===*/if ($api == 'DB_ListOrderBooks'){
		return DB_ListOrderBooks();
	} else if ($api == 'API_ListDepth'){
		return API_ListDepth(GetParam('symbol')); 
        } else if ($api == 'API_PlaceOrder' ){		
		return API_PlaceOrder(GetParam('account_id'), GetParam('symbol'), GetParam('buy'), GetParam('price'), GetParam('qty'), GetParam('leverage'), GetParam('ref_contract_id'));        
	} else if ($api == 'API_RevokeOrder'){
		return API_RevokeOrder(GetParam('account_id'), GetParam('symbol'), GetParam('order_id'));   
	} else if ($api == 'API_ListOrders'){
		return API_ListOrders(GetParam('account_id'), GetParam('filters'));        
	} else if ($api == 'API_ListContracts'){
		return API_ListContracts(GetParam('account_id'));
	} else if ($api == 'API_AddMargin'){
		return API_AddMargin(GetParam('account_id'), GetParam('symbol'), GetParam('ref_contract_id'), GetParam('margin'));
	} else if ($api == 'API_GetBalance'){
		return API_GetBalance(GetParam('account_id'));        
	} else if ($api == 'API_Deposit'){
		return API_Deposit(GetParam('account_id'),GetParam('coin'),GetParam('qty'));
	} else if ($api == 'API_Withdrawal'){
		return API_Withdrawal(GetParam('account_id'),GetParam('coin'),GetParam('qty'));
	} else {
		return "undefined api";
	}
}

#echo MainEntrance();
echo API_RemoveOrderBook('BTC/USDT-20x-0928');
echo API_RenameOrderBook('BTC/USDT-20x-1005', 'BTC/USDT 20x Nweeks');
echo API_CreateOrderBook('BTC/USDT-20x-0105', 'BTC/USDT', 'BTC/USDT-20x-Nweeks', 1500000000);
echo DB_ListOrderBooks();
?>
