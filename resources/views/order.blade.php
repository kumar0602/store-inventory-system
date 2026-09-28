<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Billing — New Order</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        :root{
            --bg:#F5F8F4;
            --ink:#20241F;
            --muted:#7A8177;
            --line:#DCE3D8;
            --teal:#1F7A5C;
            --teal-dark:#175D46;
            --apricot:#E2984B;
            --apricot-bg:#FBF1E2;
            --apricot-line:#F0D7AE;
            --white:#FFFFFF;
        }
        *{box-sizing:border-box;}
        body{
            margin:0;
            background:var(--bg);
            font-family:'Outfit', sans-serif;
            color:var(--ink);
        }
        header{
            background:var(--teal-dark);
            color:#fff;
            padding:20px 32px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        header h1{margin:0;font-size:1.15rem;font-weight:600;}
        header .tag{
            font-size:.72rem;
            color:#CFE7DC;
            background:rgba(255,255,255,.08);
            padding:5px 12px;
            border-radius:20px;
        }

        main{
            max-width:1100px;
            margin:0 auto;
            padding:32px 24px 60px;
        }

        section{margin-bottom:34px;}
        section > .sec-title{
            font-size:1.02rem;
            font-weight:600;
            margin:0 0 14px;
        }

        .banner{
            border-radius:10px;
            padding:12px 16px;
            font-size:.88rem;
            margin-bottom:22px;
        }
        .banner.ok{background:#E4F3EC;color:var(--teal-dark);}
        .banner.bad{background:#FBEAE6;color:#A8442E;}

        /* Customer row - flat, no card */
        .customer-fields{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
            max-width:640px;
        }
        .field label{
            display:block;
            font-size:.76rem;
            color:var(--muted);
            font-weight:500;
            margin-bottom:6px;
        }
        .field input{
            width:100%;
            border:1px solid var(--line);
            border-radius:10px;
            padding:10px 13px;
            font-size:.92rem;
            font-family:inherit;
            background:var(--white);
        }
        .field input:focus{outline:none;border-color:var(--teal);}
        .field input[readonly]{background:#EFF2ED;color:var(--muted);}

        /* Products + alert row */
        .row-split{
            display:grid;
            grid-template-columns:1fr 300px;
            gap:24px;
            align-items:start;
        }
        @media (max-width:820px){ .row-split{grid-template-columns:1fr;} }

        .products-box{
            border:1px solid var(--line);
            border-radius:14px;
            background:var(--white);
            overflow:hidden;
        }
        table.items{
            width:100%;
            border-collapse:collapse;
        }
        table.items thead th{
            text-align:left;
            font-size:.72rem;
            font-weight:600;
            color:var(--muted);
            padding:12px 16px;
            background:#F1F5EF;
            border-bottom:1px solid var(--line);
        }
        table.items td{
            padding:11px 16px;
            border-bottom:1px solid var(--line);
            font-size:.88rem;
        }
        table.items tr:last-child td{border-bottom:none;}
        table.items select{
            width:100%;
            border:1px solid var(--line);
            border-radius:8px;
            padding:8px 10px;
            font-family:inherit;
            font-size:.85rem;
            background:var(--white);
        }
        table.items input[type=number]{
            width:56px;
            border:1px solid var(--line);
            border-radius:8px;
            padding:7px;
            text-align:center;
            font-family:inherit;
        }
        table.items .line-total{font-weight:600;}
        table.items button.del{
            border:none;
            background:#FBEAE6;
            color:#A8442E;
            width:28px;
            height:28px;
            border-radius:8px;
            cursor:pointer;
            font-size:1rem;
            line-height:1;
        }
        table.items button.del:hover{background:#F5D6CE;}

        .add-row{
            display:flex;
            justify-content:flex-end;
            padding:14px 16px;
            border-top:1px solid var(--line);
            background:#FAFCF9;
        }
        button.add-product{
            background:var(--teal);
            color:#fff;
            border:none;
            padding:9px 18px;
            border-radius:9px;
            font-weight:600;
            font-size:.82rem;
            font-family:inherit;
            cursor:pointer;
        }
        button.add-product:hover{background:var(--teal-dark);}

        .alert-box{
            border:1px solid var(--apricot-line);
            background:var(--apricot-bg);
            border-radius:14px;
            padding:16px 18px;
        }
        .alert-box .sec-title{
            font-size:.86rem;
            color:#9C6620;
            display:flex;
            align-items:center;
            gap:8px;
            margin:0 0 12px;
        }
        .alert-box ul{list-style:none;margin:0;padding:0;}
        .alert-box li{
            display:flex;
            justify-content:space-between;
            padding:7px 0;
            font-size:.84rem;
            border-bottom:1px solid var(--apricot-line);
            color:#5B4522;
        }
        .alert-box li:last-child{border-bottom:none;}
        .alert-box .count{font-weight:600;color:#9C6620;}
        .alert-box .empty{font-size:.8rem;color:var(--muted);font-style:italic;}

        /* Payment + action row */
        .payment-box{
            border:1px solid var(--line);
            border-radius:14px;
            background:var(--white);
            padding:18px 20px;
        }
        .p-line{
            display:flex;
            justify-content:space-between;
            font-size:.9rem;
            padding:6px 0;
            color:var(--muted);
        }
        .p-line.total{
            border-top:1px dashed var(--line);
            margin-top:6px;
            padding-top:12px;
            font-size:1.05rem;
            font-weight:700;
            color:var(--ink);
        }
        .p-line.total .amt{color:var(--teal-dark);}
        .tender{margin-top:16px;}
        .tender label{
            display:block;font-size:.76rem;color:var(--muted);font-weight:500;margin-bottom:6px;
        }
        .tender input{
            width:100%;border:1px solid var(--line);border-radius:10px;padding:10px 13px;
            font-family:inherit;font-size:.92rem;
        }
        .balance-row{
            display:flex;justify-content:space-between;align-items:center;
            margin-top:12px;padding:10px 14px;background:#E4F3EC;border-radius:10px;
            font-size:.86rem;color:var(--teal-dark);font-weight:500;
        }
        .balance-row .amt{font-weight:700;font-size:1rem;}

        .action-box{
            display:flex;
            flex-direction:column;
            align-items:flex-start;
            justify-content:flex-start;
            gap:10px;
        }
        button.generate{
            width:100%;
            background:var(--teal);
            color:#fff;
            border:none;
            padding:14px;
            border-radius:12px;
            font-size:.95rem;
            font-weight:700;
            font-family:inherit;
            cursor:pointer;
        }
        button.generate:hover{background:var(--teal-dark);}
        .action-hint{font-size:.78rem;color:var(--muted);line-height:1.5;}
    </style>
</head>
<body>

    <header>
        <h1>Store Billing — New Order</h1>
        <span class="tag">POS Counter Terminal</span>
    </header>

    <main x-data="billingSystem()">

        <div x-show="message" x-cloak :class="success ? 'banner ok' : 'banner bad'" x-text="message" style="display:none;"></div>

        <section>
            <p class="sec-title">Customer</p>
            <div class="customer-fields">
                <div class="field">
                    <label>Email</label>
                    <input type="email" x-model="customer.email" @blur="checkCustomer" placeholder="e.g. thomas@example.com">
                </div>
                <div class="field">
                    <label>Name</label>
                    <input type="text" x-model="customer.name" placeholder="Auto-filled if email exists" required>
                </div>
            </div>
        </section>

        <section>
            <p class="sec-title">Products</p>
            <div class="row-split">
                <div class="products-box">
                    <table class="items">
                        <thead>
                            <tr><th>Product</th><th>Qty</th><th>Price</th><th>Line total</th><th></th></tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td>
                                        <select x-model.number="item.product_id" @change="updateProductDetails(index)">
                                            <option value="">Select product...</option>
                                            <template x-for="p in allProducts" :key="p.id">
                                                <option :value="p.id" x-text="p.name + ' (Stock: ' + p.stock_on_hand + ')'"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td><input type="number" min="1" x-model.number="item.quantity"></td>
                                    <td x-text="'₹' + item.price.toFixed(2)"></td>
                                    <td class="line-total" x-text="'₹' + calculateLineTotal(item).toFixed(2)"></td>
                                    <td><button class="del" @click="removeItem(index)">&times;</button></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div class="add-row">
                        <button class="add-product" @click="addItem">+ Add Product</button>
                    </div>
                </div>

                <div class="alert-box">
                    <p class="sec-title">⚠ Low Stock Alert</p>
                    <ul>
                        @forelse($lowStockProducts as $lowProduct)
                            <li>
                                <span>{{ $lowProduct->name }}</span>
                                <span class="count">{{ $lowProduct->stock_on_hand }} left</span>
                            </li>
                        @empty
                            <li class="empty">No low stock items right now.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </section>

        <section>
            <p class="sec-title">Payment</p>
            <div class="row-split">
                <div class="payment-box">
                    <div class="p-line"><span>Subtotal</span><span x-text="'₹' + subtotal.toFixed(2)"></span></div>
                    <div class="p-line"><span>Tax</span><span x-text="'₹' + taxTotal.toFixed(2)"></span></div>
                    <div class="p-line total"><span>Grand Total</span><span class="amt" x-text="'₹' + grandTotal.toFixed(2)"></span></div>

                    <div class="tender">
                        <label>Amount Given by Customer</label>
                        <input type="number" x-model.number="amountGiven" placeholder="Enter cash received">
                    </div>
                    <div class="balance-row">
                        <span>Balance to Return</span>
                        <span class="amt" x-text="'₹' + (amountGiven >= grandTotal ? (amountGiven - grandTotal).toFixed(2) : '0.00')"></span>
                    </div>
                </div>

                <div class="action-box">
                    <button class="generate" @click="submitOrder">Generate Bill</button>
                    <!-- <span class="action-hint">Saves the order, shows the bill on this page, and emails a PDF copy to the customer.</span> -->
                </div>
            </div>
        </section>

    </main>

    <script>
        function billingSystem() {
            return {
                allProducts: @json($products),
                customer: { email: '', name: '' },
                items: [
                    { product_id: '', quantity: 1, price: 0, tax_percentage: 0 }
                ],
                amountGiven: 0,
                message: '',
                success: false,

                addItem() {
                    this.items.push({ product_id: '', quantity: 1, price: 0, tax_percentage: 0 });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                updateProductDetails(index) {
                    const prodId = this.items[index].product_id;
                    const product = this.allProducts.find(p => p.id == prodId);
                    if (product) {
                        this.items[index].price = parseFloat(product.price_per_unit);
                        this.items[index].tax_percentage = parseFloat(product.tax_percentage || 0);
                    }
                },

                calculateLineTotal(item) {
                    const sub = (item.price || 0) * (item.quantity || 0);
                    const tax = sub * ((item.tax_percentage || 0) / 100);
                    return sub + tax;
                },

                get subtotal() {
                    return this.items.reduce((acc, item) => acc + ((item.price || 0) * (item.quantity || 0)), 0);
                },

                get taxTotal() {
                    return this.items.reduce((acc, item) => {
                        const sub = (item.price || 0) * (item.quantity || 0);
                        return acc + (sub * ((item.tax_percentage || 0) / 100));
                    }, 0);
                },

                get grandTotal() {
                    return this.subtotal + this.taxTotal;
                },

                async checkCustomer() {
                    if (!this.customer.email) return;
                    try {
                        const res = await axios.get(`/api/customers/orders?email=${this.customer.email}`);
                        if (res.data && res.data.customer) {
                            this.customer.name = res.data.customer.name;
                        }
                    } catch (e) {
                        this.customer.name = '';
                    }
                },

                async submitOrder() {
                    this.message = '';
                    try {
                        const payload = {
                            customer_email: this.customer.email,
                            customer_name: this.customer.name || 'Guest Counter Customer',
                            items: this.items.map(i => ({ product_id: i.product_id, quantity: i.quantity }))
                        };

                        const response = await axios.post('/api/orders', payload);
                        this.success = true;
                        this.message = `Order #${response.data.data.id} created successfully! Grand Total: ₹${Number(response.data.data.grand_total).toFixed(2)}`;

                        this.customer = { email: '', name: '' };
                        this.items = [{ product_id: '', quantity: 1, price: 0, tax_percentage: 0 }];
                        this.amountGiven = 0;
                    } catch (error) {
                        this.success = false;
                        this.message = error.response?.data?.error || 'Failed to generate order. Please verify input and stock.';
                    }
                }
            }
        }
    </script>
</body>
</html>