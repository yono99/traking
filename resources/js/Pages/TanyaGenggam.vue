            errorMessage,
            currentPage,
            totalPages,
            search,
            updateStatus,
            nextPage,
            prevPage,
        };
    },
};
</script>

<style scoped>
.search-container {
    padding: 20px;
}

.result-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.result-table th,
.result-table td {
    border: 1px solid #ddd;
    padding: 8px;
}

.result-table th {
    background-color: #f4f4f4;
}

.error-message {
    color: red;
    margin-top: 10px;
}

.pagination {
    margin-top: 20px;
}
</style>