import React from 'react';
import ReactDOM from 'react-dom';
import Categories from '../../components/admin/Categories';
import MessageBar from '../../components/admin/MessageBar';

try {
    ReactDOM.render(<Categories />, document.getElementById('list'));
} catch (e) {
    ReactDOM.render(<MessageBar message={`Błąd: ${e.message}`} variant="error" open={true}/>, document.getElementById('list'));
}
