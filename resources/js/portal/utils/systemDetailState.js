const queryString = (value) => typeof value === 'string' ? value : '';

export const readEndpointFilters = (query) => ({
  query: queryString(query.endpoint_q),
  module: queryString(query.endpoint_module),
});

export const withEndpointFilters = (query, filters) => ({
  ...query,
  endpoint_q: filters.query.trim() || undefined,
  endpoint_module: filters.module || undefined,
});

export const withoutEndpointFilters = (query) => {
  const { endpoint_q, endpoint_module, from_system, ...catalogQuery } = query;
  return catalogQuery;
};

export const clearDocumentResults = ({ documents, documentPage, documentTotal }) => {
  documents.value = [];
  documentPage.value = 1;
  documentTotal.value = 0;
};

export const documentFiltersPending = (draft, applied) => (
  draft.query.trim() !== applied.query || draft.module !== applied.module
);
