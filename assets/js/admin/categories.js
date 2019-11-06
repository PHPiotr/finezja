import React from 'react';
import ReactDOM from 'react-dom';
import FloatingAddButton from '../components/admin/FloatingAddButton';

ReactDOM.render(
    <FloatingAddButton href="/admin/oferta/nowa-kategoria" />
    , document.getElementById('floating-add-btn')
);

import Sortable from 'sortablejs';
import axios from 'axios';

const categories = document.getElementById('categories');
Sortable.create(categories, {
    handle: '.fa-sort',
    onEnd: async evt => {
        const {oldIndex, newIndex} = evt;
        if (oldIndex === newIndex) {
            return;
        }
        await axios({
            method: 'put',
            url: '/admin/categories/sort',
            data: {
                oldIndex,
                newIndex,
                slug: evt.item.id,
            }
        });
    },
});
