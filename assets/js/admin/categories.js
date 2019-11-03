import Sortable from 'sortablejs';
import axios from 'axios';

const categories = document.getElementById('categories');
Sortable.create(categories, {
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
