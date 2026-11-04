import {pageManager} from "../../Core/js/pageManager";
pageManager.registerController('Start', () => import('./Controllers/Start'));
pageManager.registerController('Authorization', () => import('./Controllers/Authorization'));
