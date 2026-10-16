"""Regression test for the supplied Reliance GRN sample."""

import unittest
from pathlib import Path

from document_reader import read_document
from parsers.reliance import parse


class RelianceParserTest(unittest.TestCase):
    def test_sample(self):
        sample = Path(__file__).parents[1] / "samples" / "5114091668.PDF"
        document = read_document(str(sample))
        result = parse(document["text"], document["tables"])

        self.assertEqual(result["grn_number"], "5114091668")
        self.assertEqual(result["po_number"], "5116024680")
        self.assertEqual(len(result["items"]), 4)
        self.assertEqual(
            sum(item["accepted_qty"] for item in result["items"]),
            1215,
        )


if __name__ == "__main__":
    unittest.main()
