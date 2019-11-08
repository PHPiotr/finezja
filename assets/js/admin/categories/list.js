import React from 'react';
import ReactDOM from 'react-dom';
import Categories from '../../components/admin/Categories';

ReactDOM.render(
    <Categories categories={JSON.parse(categories)} />
    , document.getElementById('list')
);
