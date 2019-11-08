import React from 'react';
import ReactDOM from'react-dom';
import NewCategory from '../../components/admin/NewCategory';

ReactDOM.render(<NewCategory category={JSON.parse(category)} />, document.getElementById('edit'));
