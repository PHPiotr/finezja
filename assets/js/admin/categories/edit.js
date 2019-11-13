import React from 'react';
import ReactDOM from 'react-dom';
import NewCategory from '../../components/admin/NewCategory';
import MessageBar from '../../components/admin/MessageBar';

try {
    ReactDOM.render(<NewCategory categoryId={categoryId} />, document.getElementById('edit'));
} catch (e) {
    ReactDOM.render(<MessageBar message={`Błąd: ${e.message}`} variant="error" open={true}/>, document.getElementById('edit'));
}

