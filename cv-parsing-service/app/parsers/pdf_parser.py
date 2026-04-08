"""
PDF Parser - Extract text from PDF files
"""
import PyPDF2
import logging

logger = logging.getLogger(__name__)


class PDFParser:
    """Parse PDF files and extract text"""
    
    @staticmethod
    def extract_text(file_path: str) -> str:
        """
        Extract text from PDF file
        
        Args:
            file_path: Path to PDF file
            
        Returns:
            Extracted text
        """
        text = []
        try:
            with open(file_path, 'rb') as pdf_file:
                pdf_reader = PyPDF2.PdfReader(pdf_file)
                
                for page_num in range(len(pdf_reader.pages)):
                    page = pdf_reader.pages[page_num]
                    text.append(page.extract_text())
            
            result = '\n'.join(text)
            logger.info(f"Successfully extracted {len(text)} pages from PDF")
            return result
            
        except PyPDF2.PdfReadError as e:
            logger.error(f"PDF read error: {e}")
            raise Exception(f"Failed to read PDF: {e}")
        except Exception as e:
            logger.error(f"Error extracting PDF text: {e}")
            raise
