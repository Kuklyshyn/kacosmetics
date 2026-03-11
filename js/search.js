/**
 * Search overlay with autocomplete functionality
 */
(function() {
    'use strict';

    var searchTimeout = null;
    var currentRequest = null;

    document.addEventListener('DOMContentLoaded', function() {
        var searchToggle = document.querySelector('.search-toggle');
        var searchOverlay = document.getElementById('search-overlay');
        var searchClose = document.querySelector('.search-overlay-close');
        var searchInput = document.getElementById('search-autocomplete-input');
        var searchResults = document.getElementById('search-autocomplete-results');
        var searchForm = document.querySelector('.search-overlay-form');

        if (!searchToggle || !searchOverlay) {
            return;
        }

        // Open search overlay
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openSearch();
        });

        // Close search overlay
        if (searchClose) {
            searchClose.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeSearch();
            });
        }

        // Close on overlay background click
        searchOverlay.addEventListener('click', function(e) {
            if (e.target === searchOverlay) {
                closeSearch();
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchOverlay.style.display === 'flex') {
                closeSearch();
            }
        });

        // Autocomplete on input
        if (searchInput && searchResults) {
            searchInput.addEventListener('input', function() {
                var term = this.value.trim();

                // Clear previous timeout
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }

                // Abort previous request
                if (currentRequest) {
                    currentRequest.abort();
                }

                // Hide results if less than 2 characters
                if (term.length < 2) {
                    searchResults.style.display = 'none';
                    searchResults.innerHTML = '';
                    return;
                }

                // Debounce search
                searchTimeout = setTimeout(function() {
                    performSearch(term);
                }, 300);
            });
        }

        function performSearch(term) {
            if (typeof kacSearch === 'undefined') {
                return;
            }

            // Show loading
            searchResults.innerHTML = '<div style="text-align:center;padding:20px;color:#999;"><span class="search-loading"></span></div>';
            searchResults.style.display = 'block';

            // Create AJAX request
            currentRequest = new XMLHttpRequest();
            currentRequest.open('GET', kacSearch.ajaxurl + '?action=kac_search_autocomplete&term=' + encodeURIComponent(term), true);

            currentRequest.onload = function() {
                if (this.status >= 200 && this.status < 400) {
                    var results = JSON.parse(this.response);
                    displayResults(results, term);
                }
            };

            currentRequest.onerror = function() {
                searchResults.style.display = 'none';
            };

            currentRequest.send();
        }

        function displayResults(results, term) {
            if (!results || results.length === 0) {
                searchResults.innerHTML = '<div style="text-align:center;padding:30px 0;color:#666;">' + kacSearch.noResults + '</div>';
                searchResults.style.display = 'block';
                return;
            }

            var html = '<div class="search-results-list">';

            results.forEach(function(product) {
                html += '<a href="' + product.url + '" class="search-result-item" style="display:flex;align-items:center;padding:15px 0;border-bottom:1px solid #eee;text-decoration:none;color:#000;transition:background 0.2s;">';
                html += '<div style="width:60px;height:60px;flex-shrink:0;margin-right:15px;background:#f5f5f5;">';
                html += '<img src="' + product.image + '" alt="' + product.title + '" style="width:100%;height:100%;object-fit:cover;">';
                html += '</div>';
                html += '<div style="flex:1;min-width:0;">';
                html += '<div style="font-size:14px;margin-bottom:5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + product.title + '</div>';
                html += '<div style="font-size:14px;color:#000;font-weight:500;">' + product.price + '</div>';
                html += '</div>';
                html += '</a>';
            });

            html += '</div>';

            // Add "View all results" link
            if (results.length >= 5) {
                html += '<a href="' + searchForm.action + '?s=' + encodeURIComponent(term) + '&post_type=product" style="display:block;text-align:center;padding:20px;color:#000;text-decoration:none;font-size:13px;text-transform:uppercase;letter-spacing:1px;border-top:1px solid #eee;margin-top:10px;">';
                html += kacSearch.viewAll + ' &rarr;';
                html += '</a>';
            }

            searchResults.innerHTML = html;
            searchResults.style.display = 'block';
        }

        function openSearch() {
            searchOverlay.style.display = 'flex';
            searchOverlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            // Focus on input after a short delay
            setTimeout(function() {
                if (searchInput) {
                    searchInput.focus();
                }
            }, 100);
        }

        function closeSearch() {
            searchOverlay.style.display = 'none';
            searchOverlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';

            // Clear search
            if (searchInput) {
                searchInput.value = '';
            }
            if (searchResults) {
                searchResults.style.display = 'none';
                searchResults.innerHTML = '';
            }
        }
    });
})();
