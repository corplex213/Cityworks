document.addEventListener('DOMContentLoaded', () => {
    const tableHeaders = document.querySelectorAll('th.resizable');

    tableHeaders.forEach(header => {
        const resizer = document.createElement('div');
        resizer.classList.add('resizer');
        header.appendChild(resizer);

        let startX, startWidth;

        resizer.addEventListener('mousedown', (e) => {
            startX = e.pageX;
            startWidth = header.offsetWidth;

            document.addEventListener('mousemove', resizeColumn);
            document.addEventListener('mouseup', stopResize);
        });

        function resizeColumn(e) {
            const newWidth = startWidth + (e.pageX - startX);
            header.style.width = `${newWidth}px`;

            // Apply the same width to all cells in the column
            const columnIndex = Array.from(header.parentElement.children).indexOf(header);
            const table = header.closest('table');
            table.querySelectorAll(`tr td:nth-child(${columnIndex + 1})`).forEach(cell => {
                cell.style.width = `${newWidth}px`;
            });
        }

        function stopResize() {
            document.removeEventListener('mousemove', resizeColumn);
            document.removeEventListener('mouseup', stopResize);
        }
        });
    });