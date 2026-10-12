import assert from 'node:assert/strict';
import test from 'node:test';
import {
  clearDocumentResults,
  documentFiltersPending,
  readEndpointFilters,
  withEndpointFilters,
  withoutEndpointFilters,
} from '../../resources/js/portal/utils/systemDetailState.js';

test('failed document loads remove stale cards and pagination', () => {
  const documents = { value: [{ id: 7, title: 'Private manual', download_url: '/documents/7/file' }] };
  const documentPage = { value: 2 };
  const documentTotal = { value: 31 };

  clearDocumentResults({ documents, documentPage, documentTotal });

  assert.deepEqual(documents.value, []);
  assert.equal(documentPage.value, 1);
  assert.equal(documentTotal.value, 0);
});

test('endpoint filters survive detail navigation without contaminating catalog search', () => {
  const catalogQuery = { q: 'inventory', method: 'GET' };
  const filtered = withEndpointFilters(catalogQuery, { query: 'orders', module: '12' });

  assert.deepEqual(readEndpointFilters(filtered), { query: 'orders', module: '12' });
  assert.deepEqual(withoutEndpointFilters(filtered), catalogQuery);
  assert.deepEqual(catalogQuery, { q: 'inventory', method: 'GET' });
});

test('document pagination is pending when draft filters differ from applied filters', () => {
  assert.equal(documentFiltersPending({ query: 'new', module: '3' }, { query: 'old', module: '3' }), true);
  assert.equal(documentFiltersPending({ query: 'old', module: '4' }, { query: 'old', module: '3' }), true);
  assert.equal(documentFiltersPending({ query: ' old ', module: '3' }, { query: 'old', module: '3' }), false);
});
