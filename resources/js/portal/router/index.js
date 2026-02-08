import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import EndpointDetailView from '../views/EndpointDetailView.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: HomeView },
    { path: '/endpoints/:publicId', name: 'endpoint-detail', component: EndpointDetailView, props: true },
  ],
  scrollBehavior() {
    return { top: 0 };
  },
});

export default router;
