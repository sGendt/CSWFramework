
function basket_shortlink_push(data)
{
	$('.basket_shortlink .price').html(data.total);
	$('.basket_shortlink .qty span').html(data.qty);
}