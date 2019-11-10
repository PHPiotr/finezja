import React from 'react';
import ReactDOM from 'react-dom';
import Categories from '../../components/admin/Categories';
import MessageBar from '../../components/admin/MessageBar';

try {
    const categoriesParsed = JSON.parse(categories.replace(/\n+/, '\\n'));
    ReactDOM.render(<Categories categories={categoriesParsed} />, document.getElementById('list'));
} catch (e) {
    ReactDOM.render(<MessageBar message={`Błąd poodczas parsoowania kategorii: ${e.message}`} variant="error" open={true}/>, document.getElementById('list'));
}
