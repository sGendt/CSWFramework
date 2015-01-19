$(document).ready
(
	function()
	{
		$('.increfield').cswIncrefield();

		$('#btn_add').click
		(
			function()
			{
				getLoader('Ajout au panier');

				var id = $(this).data('id');
				var qty = $('#field_qty').val();

				ajax
				(
					'http://' + window.location.hostname + '/', 
					'action=ajax&requested=addBasket&basket_add=true&id=' + id + '&qty=' + qty, 
					onAddBasket
				);
			}
		);
	}
);


function onAddBasket(datas)
{
	basket_shortlink_push(datas.datas);
	removeLoader();
}