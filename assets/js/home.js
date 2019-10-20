import React from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

const latestProductsUrl = process.env.LATEST_PRODUCTS_API_URL;
const latestProductsSrc = process.env.LATEST_PRODUCTS_SRC_PATH;

import Products from './components/Products';

(async () => {
    try {
        const {data} = await axios.get(latestProductsUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Sec-Fetch-Site': 'cross-site',
            },
        });
        ReactDOM.render(
            <Products products={data} src={latestProductsSrc} />,
            document.getElementById('latest-products')
        );
    } catch {
        document.getElementById('latest-products').innerHTML = '';
    }
})();
