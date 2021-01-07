import './styles/app.scss';
import 'popper.js';
import 'bootstrap';

$(".btn-remove-file").on("click", e => {
    $(e.currentTarget.dataset.target).val('');
});