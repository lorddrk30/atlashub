import { computed, reactive, ref } from 'vue';
import { defineStore } from 'pinia';

export const useCatalogSearchStore = defineStore('catalogSearch', () => {
  const loading = ref(false);
  const initialized = ref(false);

  const filters = reactive({
    systems: [],
    modules: [],
    methods: [],
    authentication_types: [],
    artefact_types: [],
  });

  const query = reactive({
    q: '',
    system_id: '',
    module_id: '',
    method: '',
    authentication_type: '',
    artefact_type: '',
  });

  const results = reactive({
    total: 0,
    counts: {
      systems: 0,
      modules: 0,
      endpoints: 0,
      artefacts: 0,
    },
    grouped: {
      systems: [],
      modules: [],
      endpoints: [],
      artefacts: [],
    },
  });

  const activeParams = computed(() => {
    const params = new URLSearchParams();

    if (query.q) params.set('q', query.q);
    if (query.system_id) params.set('system_id', query.system_id);
    if (query.module_id) params.set('module_id', query.module_id);
    if (query.method) params.set('method', query.method);
    if (query.authentication_type) params.set('authentication_type', query.authentication_type);
    if (query.artefact_type) params.set('artefact_type', query.artefact_type);

    return params;
  });

  const hydrateQuery = (routeQuery) => {
    query.q = routeQuery.q || '';
    query.system_id = routeQuery.system_id || '';
    query.module_id = routeQuery.module_id || '';
    query.method = routeQuery.method || '';
    query.authentication_type = routeQuery.authentication_type || '';
    query.artefact_type = routeQuery.artefact_type || '';
  };

  const fetchFilters = async () => {
    const response = await fetch('/api/v1/filters');
    const data = await response.json();

    filters.systems = data.systems || [];
    filters.modules = data.modules || [];
    filters.methods = data.methods || [];
    filters.authentication_types = data.authentication_types || [];
    filters.artefact_types = data.artefact_types || [];
  };

  const search = async () => {
    loading.value = true;

    try {
      const response = await fetch(`/api/v1/search?${activeParams.value.toString()}`);
      const data = await response.json();

      results.total = data.total || 0;
      results.counts = data.counts || { systems: 0, modules: 0, endpoints: 0, artefacts: 0 };
      results.grouped = data.grouped || { systems: [], modules: [], endpoints: [], artefacts: [] };
    } finally {
      loading.value = false;
      initialized.value = true;
    }
  };

  return {
    loading,
    initialized,
    filters,
    query,
    results,
    activeParams,
    hydrateQuery,
    fetchFilters,
    search,
  };
});
