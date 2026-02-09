import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import EndpointDetailView from '../views/EndpointDetailView.vue';
import ReportsDashboardView from '../views/ReportsDashboard.vue';
import SystemDetailView from '../views/SystemDetailView.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: HomeView },
    { path: '/systems/:systemId', name: 'system-detail', component: SystemDetailView, props: true },
    { path: '/endpoints/:publicId', name: 'endpoint-detail', component: EndpointDetailView, props: true },
    { path: '/reports', name: 'reports-dashboard', component: ReportsDashboardView },
  ],
  scrollBehavior() {
    return { top: 0 };
  },
});

export default router;
