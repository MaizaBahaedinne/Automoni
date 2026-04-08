"""
PDF Parser - Extract text from PDF files using pdfplumber

pdfplumber provides layout-aware text extraction that handles columns,
tables, and multi-column CVs far better than PyPDF2.
"""
import pdfplumber
import logging

logger = logging.getLogger(__name__)


class PDFParser:
    """Parse PDF files and extract text using pdfplumber"""

    @staticmethod
    def extract_text(file_path: str) -> str:
        """
        Extract text from PDF using pdfplumber.

        Joins all pages with double newlines so paragraph structure is
        preserved for downstream NLP and LLM processing.

        Args:
            file_path: Path to PDF file

        Returns:
            Extracted text (may be empty string if the PDF has no text layer)
        """
        pages = []
        try:
            with pdfplumber.open(file_path) as pdf:
                for page in pdf.pages:
                    page_text = page.extract_text(x_tolerance=3, y_tolerance=3)
                    if page_text:
                        pages.append(page_text)

            result = "\n\n".join(pages)
            logger.info("Extracted %d pages from PDF (%d chars)", len(pages), len(result))
            return result

        except Exception as e:
            logger.error("PDF extraction error: %s", e)
            raise Exception(f"Failed to read PDF: {e}") from e
