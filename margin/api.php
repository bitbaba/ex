<?php
function RPC_HOST()
{
	return "127.0.0.1";
}

function RPC_PORT()
{
	return "10086";
}

function req($method, $params){
        $url = 'http://' . RPC_HOST() . ':'. RPC_PORT() .'/';
        $header[] = "content-type: text/plain";
        $json_req = '{"jsonrpc":"1.0", "id":"qm", "method":"'.$method.'", "params":'.$params.'}';
        //echo $json_req;
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

function list_orderbooks(){
	return req("list_orderbooks"
		, '[]');
}

function list_depth($symbol){
	return req("list_depth"
		, '["'.$symbol.'"]');
}

function list_contracts($account_id){
	return req("list_contracts", '['.$account_id.']');
}

function list_orders($account_id){
	return req("list_orders", '['.$account_id.']');
}

function revoke_order($account_id, $symbol, $order_id){
	return req("revoke_order", '['.$account_id.',"'.$symbol.'",'.$order_id.']');
}

function __place_order($account_id, $symbol, $buy, $price, $qty, $leverage,$ref_contract_id){
	return req("place_order"
		, '['.$account_id.', "'.$symbol.'", '.$buy.', "'.$price.'", "'.$qty.'", "'.$leverage.'", '.$ref_contract_id.']');
}

function _buyy($account_id, $symbol, $price, $qty, $leverage, $ref_contract_id){
	return __place_order($account_id, $symbol, 'true'/*buy or long*/, $price, $qty, $leverage, $ref_contract_id);
}

function _sell($account_id, $symbol, $price, $qty,$leverage, $ref_contract_id){
	return __place_order($account_id, $symbol, 'false'/*sell or short*/, $price, $qty,$leverage,$ref_contract_id);
}

function long_open($account_id, $symbol, $price, $qty, $leverage){
	return _buyy($account_id, $symbol, $price, $qty, $leverage, '0');
}

function short_open($account_id, $symbol, $price, $qty, $leverage){
	return _sell($account_id, $symbol, $price, $qty, $leverage, '0');
}

function short_close($account_id, $symbol, $price, $qty, $leverage, $ref_contract_id){
	return _buyy($account_id, $symbol, $price, $qty, $leverage, $ref_contract_id);
}

function long_close($account_id, $symbol, $price, $qty, $leverage, $ref_contract_id){
	return _sell($account_id, $symbol, $price, $qty,$leverage, $ref_contract_id);
}

function deposit($account_id, $coin, $qty){
	return req("deposit", '['.$account_id.',"'.$coin.'","'.$qty.'"]');
}
function withdrawal($account_id, $coin, $qty){
	return req("withdrawal", '['.$account_id.',"'.$coin.'","'.$qty.'"]');
}
function get_balance($account_id){
	return req("get_balance", '['.$account_id.']');
}

function get_trades($symbol, $begin, $limit){
	return req("get_trades", '["'.$symbol.'",'.$begin.','.$limit.']');
}

function GetParam($name){
	if (isset($_REQUEST[$name])) 
        return $_REQUEST[$name];
}

function TokenCheck(){
	return true;//(GetParam("token") == "secure");
}

function dispatch(){
	if (!TokenCheck()){ 
		return "auth failed";
	}

	$api=GetParam("api");
	if ($api == 'list_orderbooks'){
		return list_orderbooks();
	} else if ($api == 'list_depth'){
		return list_depth(GetParam('symbol')); 
	} else if ($api == 'list_contracts'){
		return list_contracts(GetParam('account_id'));
	} else if ($api == 'list_orders'){
		return list_orders(GetParam('account_id'));
	} else if ($api == 'revoke_order'){
		return revoke_order(GetParam('account_id'), GetParam('symbol'), GetParam('order_id'));
	} else if ($api == 'long_open'){
		return long_open(GetParam('account_id'), GetParam('symbol'), GetParam('price'), GetParam('qty'), GetParam('leverage'));
    } else if ($api == 'short_close' ){		
		return short_close(GetParam('account_id'), GetParam('symbol'), GetParam('price'), GetParam('qty'), GetParam('leverage'), GetParam('ref_contract_id'));
	} else if ($api == 'short_open') { 
		return short_open(GetParam('account_id'), GetParam('symbol'), GetParam('price'), GetParam('qty'), GetParam('leverage'));
    } else if ($api == 'long_close' ){
		return long_close(GetParam('account_id'), GetParam('symbol'), GetParam('price'), GetParam('qty'), GetParam('leverage'), GetParam('ref_contract_id'));
	} else if ($api == 'deposit'){
		return deposit(GetParam('account_id'),GetParam('coin'),GetParam('qty'));
	} else if ($api == 'withdrawal'){
		return withdrawal(GetParam('account_id'),GetParam('coin'),GetParam('qty'));
	} else if ($api == 'get_balance'){
		return get_balance(GetParam('account_id'));
	} else if ($api == 'get_trades'){
		return get_trades(GetParam('symbol'), GetParam('begin'), GetParam('limit'));
	} else {
		return "undefined api";
	}
}

echo dispatch();

?>
