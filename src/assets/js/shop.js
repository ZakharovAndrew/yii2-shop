class ZaanShop {
    constructor() {
        console.log('START SHOP');
    }
    
    addCart = (id, show_alert = true) => {
        let self = this;
        $.ajax({
            url: '/shop/cart/add',
            type: 'POST',
            data: {
                productId: id,
                quantity: 1
            },
            success: function(response) {
                if (response.success) {
                    let product_counter = document.getElementById('product-counter-'+id);
                    if (product_counter) {
                        product_counter.innerHTML = response.quantity;
                        let product_cost = document.getElementById('product-cost-'+id);
                        product_cost.innerHTML = (response.quantity * product_cost.dataset.cost) + ' ₽';

                        self.cartSumCount();
                    } else {
                        console.log('Не нашел ' + 'product-counter-'+id);
                    }
                    
                    if (show_alert) {
                        alert('Товар добавлен в корзину!');
                    }
                    
                } else {
                    alert('Ошибка: ' + response.message);
                }
            }
        });
    }
    
    cartSumCount = () => {
        let sumProducts = 0;
        let sumCosts = 0;

        const products = document.querySelectorAll('.cart-product');
        products.forEach(product => {
            let id = product.dataset.id;

            // sum products
            let product_counter = document.getElementById('product-counter-'+id).innerHTML;
            sumProducts += (product_counter * 1);

            // sum cost
            let product_cost = document.getElementById('product-cost-'+id).dataset.cost;
            sumCosts += (product_counter * 1) * (product_cost * 1)
        });

        // update sum
        document.querySelector('#products-counter').innerHTML =  sumProducts;
        document.querySelector('#products-cost').innerHTML =  sumCosts;

        let el_min_cost = document.getElementById('min-cost');

        if (el_min_cost) {
            let min_cost = el_min_cost.innerHTML * 1;

            // check min cost
            if (sumCosts < min_cost) {
                document.getElementById("btn-create-order").disabled = true;
            } else {
                document.getElementById("btn-create-order").disabled = false;
            }

            // refresh cart
            if (sumProducts == 0) {
                this.cart();
            }
        }
    }
    
    minusCart = (id) => {
        let self = this;
        $.ajax({
            url: '/shop/cart/decrease',
            type: 'POST',
            data: {
                productId: id,
                quantity: 1
            },
            success: function(response) {
                if (response.success) {
                    let product_counter = document.getElementById('product-counter-'+id);
                    if (product_counter) {
                        product_counter.innerHTML = response.quantity;
                        let product_cost = document.getElementById('product-cost-'+id);
                        product_cost.innerHTML = (response.quantity * product_cost.dataset.cost) + ' ₽';

                        if (response.quantity == 0) {
                            console.log('clear row');
                            document.querySelector('#cart-row-' + id).remove();
                        }
                        
                        self.cartSumCount();
                    } else {
                        console.log('Не нашел ' + 'product-counter-'+id);
                    }
                } else {
                    alert('Ошибка: ' + response.message);
                }
            }
        });
    }
    
    removeFromCart = (id) => {
        $.ajax({
            url: '/shop/cart/remove',
            type: 'POST',
            data: {
                productId: id,
            },
            success: function(response) {
                if (response.success) {
                    document.querySelector('#cart-row-' + id).remove();
                    self.cartSumCount();
                } else {
                    alert('Ошибка: ' + response.message);
                }
            }
        });
    }
}

let shop = new ZaanShop();