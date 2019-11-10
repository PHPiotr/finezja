import React from 'react';
import ReactDOM from 'react-dom';
import NewCategory from '../../components/admin/NewCategory';
import MessageBar from '../../components/admin/MessageBar';

try {
     const categoryParsed = JSON.parse(category.replace(/\n+/, '\\n'));
    ReactDOM.render(<NewCategory category={categoryParsed}/>, document.getElementById('edit'));
} catch (e) {
    ReactDOM.render(<MessageBar message={`Błąd poodczas parsoowania kategorii: ${e.message}`} variant="error" open={true}/>, document.getElementById('edit'));
}

