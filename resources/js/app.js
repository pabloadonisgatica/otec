import './bootstrap';

import Alpine from 'alpinejs';
import { detailModal } from './modals';

window.Alpine = Alpine;
window.detailModal = detailModal;

Alpine.start();
