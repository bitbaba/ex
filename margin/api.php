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

function list_orderbooks(){
	return req("list_orderbooks"
		, '[]');
}

function list_depth($symbol){
	return req("list_depth"
		, '["'.$symbol.'"]');
}

function list_orders($account_id){
	return req("list_orders", '['.$account_id.']');
}

function revoke_order($account_id, $symbol, $order_id){
	return req("revoke_order", '['.$account_id.',"'.$symbol.'",'.$order_id.']');
}

function place_order($account_id, $symbol, $buy, $price, $qty, $leverage,$ref_contract_id){
	return req("place_order"
		, '['.$account_id.', "'.$symbol.'", '.$buy.', "'.$price.'", "'.$qty.'", "'.$leverage.'", '.$ref_contract_id.']');
}

function buyy($account_id, $symbol, $price, $qty, $leverage, $ref_contract_id){
	return place_order($account_id, $symbol, 'true', $price, $qty, $leverage, $ref_contract_id);
}

function sell($account_id, $symbol, $price, $qty,$leverage, $ref_contract_id){
	return place_order($account_id, $symbol, 'false', $price, $qty,$leverage,$ref_contract_id);
}

function get_balance($account_id){
	return req("get_balance", '['.$account_id.']');
}

function get_marketinfo($symbol){
	return req("get_marketinfo", '["'.$symbol.'"]');
}

function get_trades($symbol, $begin, $limit){
	return req("get_trades", '["'.$symbol.'",'.$begin.','.$limit.']');
}

function GetParam($name){
	if (isset($_REQUEST[$name])) 
        return $_REQUEST[$name];
}

function TokenCheck(){
	return (GetParam("token") == "secure");
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
	} else if ($api == 'list_orders'){
		return list_orders(GetParam('account_id'));
	} else if ($api == 'revoke_order'){
		return revoke_order(GetParam('account_id'), GetParam('symbol'), GetParam('order_id'));
	} else if ($api == 'buyy'){
		return buyy(GetParam('account_id'), GetParam('symbol'), GetParam('price'), GetParam('qty'), GetParam('leverage'), GetParam('ref_contract_id'));
	} else if ($api == 'sell'){
		return sell(GetParam('account_id'), GetParam('symbol'), GetParam('price'), GetParam('qty'), GetParam('leverage'), GetParam('ref_contract_id'));
	} else if ($api == 'get_balance'){
		return get_balance(GetParam('account_id'));
	} else if ($api == 'get_marketinfo'){
		return get_marketinfo(GetParam('symbol'));
	} else if ($api == 'get_trades'){
		return get_trades(GetParam('symbol'), GetParam('begin'), GetParam('limit'));
	} else {
		return "undefined api";
	}
}

echo dispatch();

?>
